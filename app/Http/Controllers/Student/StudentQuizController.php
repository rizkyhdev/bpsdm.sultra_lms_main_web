<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class StudentQuizController extends Controller
{
    /**
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Menampilkan daftar quiz untuk sub-modul.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $subModuleId = $request->get('sub_module_id');
        
        if (!$subModuleId) {
            abort(400, 'Sub-module ID diperlukan.');
        }

        // Periksa apakah user sudah terdaftar dalam kursus
        $subModule = \App\Models\SubModule::with('module.course')->find($subModuleId);
        if (!$subModule) {
            abort(404, 'Sub-module tidak ditemukan.');
        }

        $enrollment = $user->userEnrollments()
            ->where('course_id', $subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses quiz.');
        }

        // Mendapatkan quiz untuk sub-modul
        $quizzes = Quiz::where('sub_module_id', $subModuleId)
            ->with(['subModule.module.course'])
            ->get();

        // Mendapatkan percobaan quiz user untuk sub-modul ini
        $quizAttempts = $user->quizAttempts()
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->with('quiz')
            ->get()
            ->keyBy('quiz_id');

        return view('student.quizzes.index', compact('quizzes', 'quizAttempts', 'subModule'));
    }

    /**
     * Display the specified quiz.
     */
    public function show(Quiz $quiz): View
    {
        $user = Auth::user();
        
        // Check if user is enrolled in the course
        $enrollment = $user->userEnrollments()
            ->where('course_id', $quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses quiz.');
        }

        // Check if user has active attempts
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        // Get user's previous attempts
        $previousAttempts = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', '!=', 'in_progress')
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if user can take the quiz
        $canTakeQuiz = $this->canTakeQuiz($user, $quiz);

        return view('student.quizzes.show', compact(
            'quiz',
            'enrollment',
            'activeAttempt',
            'previousAttempts',
            'canTakeQuiz'
        ));
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(Quiz $quiz): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        // Check if user can take the quiz
        if (!$this->canTakeQuiz($user, $quiz)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengambil quiz ini saat ini.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create new quiz attempt
            $attempt = $user->quizAttempts()->create([
                'quiz_id' => $quiz->id,
                'status' => 'in_progress',
                'started_at' => now(),
                'score' => 0
            ]);

            // Get quiz questions with answer options
            $questions = $quiz->questions()
                ->with('answerOptions')
                ->orderBy('urutan')
                ->get();

            // Initialize user answers
            foreach ($questions as $question) {
                UserAnswer::create([
                    'user_id' => $user->id,
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_answer_option_id' => null,
                    'is_correct' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz dimulai!',
                'attempt_id' => $attempt->id,
                'total_questions' => $questions->count(),
                'time_limit' => $quiz->time_limit ?? null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulai quiz.'
            ], 500);
        }
    }

    /**
     * Submit quiz answers.
     */
    public function submit(Quiz $quiz, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.selected_answer_option_id' => 'nullable|exists:answer_options,id'
        ]);

        try {
            DB::beginTransaction();

            // Get the quiz attempt
            $attempt = $user->quizAttempts()
                ->where('id', $request->attempt_id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attempt quiz tidak valid.'
                ], 400);
            }

            // Process answers and calculate score
            $totalQuestions = count($request->answers);
            $correctAnswers = 0;
            $userAnswers = [];

            foreach ($request->answers as $answerData) {
                $question = Question::with('answerOptions')->find($answerData['question_id']);
                $selectedOptionId = $answerData['selected_answer_option_id'] ?? null;
                
                // Find correct answer
                $correctOption = $question->answerOptions()->where('is_correct', true)->first();
                $isCorrect = $correctOption && $selectedOptionId == $correctOption->id;
                
                if ($isCorrect) {
                    $correctAnswers++;
                }

                // Update user answer
                UserAnswer::where([
                    'user_id' => $user->id,
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id
                ])->update([
                    'selected_answer_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect
                ]);

                $userAnswers[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->pertanyaan,
                    'selected_answer' => $selectedOptionId,
                    'correct_answer' => $correctOption ? $correctOption->id : null,
                    'is_correct' => $isCorrect
                ];
            }

            // Calculate score
            $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
            
            // Determine if passed
            $isPassed = $score >= $quiz->nilai_minimum;
            $status = $isPassed ? 'passed' : 'failed';

            // Update attempt
            $attempt->update([
                'status' => $status,
                'score' => $score,
                'completed_at' => now()
            ]);

            // If passed, check if sub-module can be marked as completed
            if ($isPassed) {
                $this->checkQuizCompletion($user, $quiz);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isPassed ? 'Selamat! Anda lulus quiz.' : 'Maaf, Anda belum lulus quiz.',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'score' => $score,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'is_passed' => $isPassed,
                    'minimum_score' => $quiz->nilai_minimum,
                    'user_answers' => $userAnswers
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim quiz.'
            ], 500);
        }
    }

    /**
     * Display quiz results.
     */
    public function result(QuizAttempt $attempt): View
    {
        $user = Auth::user();
        
        // Check if attempt belongs to user
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat mengakses hasil quiz ini.');
        }

        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $attempt->quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini.');
        }

        // Get detailed results
        $userAnswers = $attempt->userAnswers()
            ->with(['question.answerOptions'])
            ->get();

        $totalQuestions = $userAnswers->count();
        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $score = $attempt->score;
        $isPassed = $attempt->status === 'passed';

        return view('student.quizzes.result', compact(
            'attempt',
            'userAnswers',
            'totalQuestions',
            'correctAnswers',
            'score',
            'isPassed'
        ));
    }

    /**
     * Review a previous quiz attempt.
     */
    public function reviewAttempt(QuizAttempt $attempt): View
    {
        $user = Auth::user();
        
        // Check if attempt belongs to user
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat mengakses attempt quiz ini.');
        }

        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $attempt->quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini.');
        }

        // Get detailed review
        $userAnswers = $attempt->userAnswers()
            ->with(['question.answerOptions'])
            ->get();

        $totalQuestions = $userAnswers->count();
        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $score = $attempt->score;
        $isPassed = $attempt->status === 'passed';

        return view('student.quizzes.review', compact(
            'attempt',
            'userAnswers',
            'totalQuestions',
            'correctAnswers',
            'score',
            'isPassed'
        ));
    }

    /**
     * Get quiz questions for AJAX requests.
     */
    public function getQuestions(Quiz $quiz): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        // Get active attempt
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$activeAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada attempt aktif untuk quiz ini.'
            ], 400);
        }

        // Get questions with answer options (shuffled)
        $questions = $quiz->questions()
            ->with(['answerOptions' => function ($query) {
                $query->inRandomOrder(); // Shuffle answer options
            }])
            ->inRandomOrder() // Shuffle questions
            ->get();

        // Format questions for frontend
        $formattedQuestions = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'pertanyaan' => $question->pertanyaan,
                'jenis' => $question->jenis,
                'urutan' => $question->urutan,
                'answer_options' => $question->answerOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'teks' => $option->teks
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'attempt_id' => $activeAttempt->id,
                'quiz' => [
                    'id' => $quiz->id,
                    'judul' => $quiz->judul,
                    'deskripsi' => $quiz->deskripsi,
                    'nilai_minimum' => $quiz->nilai_minimum,
                    'time_limit' => $quiz->time_limit
                ],
                'questions' => $formattedQuestions,
                'total_questions' => $questions->count()
            ]
        ]);
    }

    /**
     * Save quiz progress (for auto-save functionality).
     */
    public function saveProgress(Quiz $quiz, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->userEnrollments()
            ->where('course_id', $quiz->subModule->module->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.selected_answer_option_id' => 'nullable|exists:answer_options,id'
        ]);

        try {
            // Get the quiz attempt
            $attempt = $user->quizAttempts()
                ->where('id', $request->attempt_id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attempt quiz tidak valid.'
                ], 400);
            }

            // Save answers without calculating score
            foreach ($request->answers as $answerData) {
                UserAnswer::where([
                    'user_id' => $user->id,
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $answerData['question_id']
                ])->update([
                    'selected_answer_option_id' => $answerData['selected_answer_option_id'] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Progress berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan progress.'
            ], 500);
        }
    }

    /**
     * Check if user can take the quiz.
     */
    private function canTakeQuiz($user, Quiz $quiz): bool
    {
        // Check if user has reached max attempts
        $attemptCount = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', '!=', 'in_progress')
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return false;
        }

        // Check if user has active attempt
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            return true; // Can continue existing attempt
        }

        // Check if user has passed the quiz
        $passedAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'passed')
            ->first();

        if ($passedAttempt) {
            return false; // Already passed
        }

        return true;
    }

    /**
     * Check if quiz completion allows sub-module completion.
     */
    private function checkQuizCompletion($user, Quiz $quiz): void
    {
        $subModule = $quiz->subModule;
        
        // Check if all quizzes in sub-module are passed
        $totalQuizzes = $subModule->quizzes()->count();
        $passedQuizzes = $subModule->quizzes()
            ->whereHas('quizAttempts', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'passed');
            })
            ->count();

        if ($totalQuizzes > 0 && $passedQuizzes >= $totalQuizzes) {
            // All quizzes passed, check if sub-module can be marked as completed
            $this->checkSubModuleCompletion($user, $subModule->id);
        }
    }

    /**
     * Check if sub-module is completed and update progress.
     */
    private function checkSubModuleCompletion($user, $subModuleId): void
    {
        $subModule = \App\Models\SubModule::find($subModuleId);
        
        if (!$subModule) {
            return;
        }

        $totalContents = $subModule->contents()->count();
        $completedContents = $subModule->contents()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        if ($totalContents > 0 && $completedContents >= $totalContents) {
            // Sub-module is completed, check if module is completed
            $this->checkModuleCompletion($user, $subModule->module_id);
        }
    }

    /**
     * Check if module is completed and update progress.
     */
    private function checkModuleCompletion($user, $moduleId): void
    {
        $module = \App\Models\Module::find($moduleId);
        
        if (!$module) {
            return;
        }

        $totalSubModules = $module->subModules()->count();
        $completedSubModules = $module->subModules()
            ->whereHas('userProgress', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', true);
            })
            ->count();

        if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules) {
            // Module is completed, check if course is completed
            $this->checkCourseCompletion($user, $module->course_id);
        }
    }

    /**
     * Check if course is completed and update enrollment status.
     */
    private function checkCourseCompletion($user, $courseId): void
    {
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return;
        }

        $course = $enrollment->course;
        $totalModules = $course->modules()->count();
        $completedModules = 0;

        foreach ($course->modules as $module) {
            $totalSubModules = $module->subModules()->count();
            $completedSubModules = $module->subModules()
                ->whereHas('userProgress', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_completed', true);
                })
                ->count();

            if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules) {
                $completedModules++;
            }
        }

        if ($completedModules >= $totalModules) {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Create JP record for course completion
            $this->createJpRecord($user, $course);
        }
    }

    /**
     * Create JP record when course is completed.
     */
    private function createJpRecord($user, $course): void
    {
        // Check if JP record already exists for this course
        $existingJpRecord = $user->jpRecords()
            ->where('course_id', $course->id)
            ->first();

        if (!$existingJpRecord) {
            $user->jpRecords()->create([
                'course_id' => $course->id,
                'jp_value' => $course->jp_value,
                'earned_at' => now()
            ]);
        }
    }
} 