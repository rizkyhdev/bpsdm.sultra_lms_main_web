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
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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
        
        // Get course ID from quiz (quiz can be at course, module, or sub-module level)
        $courseId = $this->getCourseIdFromQuiz($quiz);
        
        if (!$courseId) {
            abort(404, 'Kursus tidak ditemukan untuk quiz ini.');
        }
        
        // Check if user is enrolled in the course (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini untuk mengakses quiz.');
        }

        // Check if user has active attempts (not completed yet)
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        // Get user's previous attempts (completed attempts)
        $previousAttempts = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
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
        
        // Get course ID from quiz
        $courseId = $this->getCourseIdFromQuiz($quiz);
        
        if (!$courseId) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan untuk quiz ini.'
            ], 404);
        }
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

            // Get the next attempt number
            $attemptNumber = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->max('attempt_number') ?? 0;
            $attemptNumber++;

            // Create new quiz attempt
            $attempt = $user->quizAttempts()->create([
                'quiz_id' => $quiz->id,
                'nilai' => 0,
                'is_passed' => false,
                'attempt_number' => $attemptNumber,
                'started_at' => now(),
                'completed_at' => null
            ]);

            // Get quiz questions with answer options
            $questions = $quiz->questions()
                ->with('answerOptions')
                ->orderBy('urutan')
                ->get();

            // Don't create UserAnswer records yet - they will be created when user submits answers
            // UserAnswer records require answer_option_id which is not nullable

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
            \Log::error('Error starting quiz: ' . $e->getMessage(), [
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memulai quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit quiz answers.
     */
    public function submit(Quiz $quiz, Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Get course ID from quiz
        $courseId = $this->getCourseIdFromQuiz($quiz);
        
        if (!$courseId) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan untuk quiz ini.'
            ], 404);
        }
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

            // Get the quiz attempt (must be in progress, i.e., not completed)
            $attempt = $user->quizAttempts()
                ->where('id', $request->attempt_id)
                ->where('quiz_id', $quiz->id)
                ->whereNull('completed_at')
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
                
                if (!$selectedOptionId) {
                    // Skip if no answer selected
                    continue;
                }
                
                // Find correct answer
                $correctOption = $question->answerOptions()->where('is_correct', true)->first();
                $isCorrect = $correctOption && $selectedOptionId == $correctOption->id;
                
                if ($isCorrect) {
                    $correctAnswers++;
                }

                // Create or update user answer
                // UserAnswer table only has: quiz_attempt_id, question_id, answer_option_id
                UserAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id
                    ],
                    [
                        'answer_option_id' => $selectedOptionId
                    ]
                );

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

            // Update attempt
            $attempt->update([
                'nilai' => $score,
                'is_passed' => $isPassed,
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

        // Get course ID from quiz
        $courseId = $this->getCourseIdFromQuiz($attempt->quiz);
        
        if (!$courseId) {
            abort(404, 'Kursus tidak ditemukan untuk quiz ini.');
        }

        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini.');
        }

        // Get detailed results
        $userAnswers = $attempt->userAnswers()
            ->with(['question.answerOptions', 'answerOption'])
            ->get();

        // Calculate is_correct for each answer (not stored in database)
        $userAnswers = $userAnswers->map(function ($userAnswer) {
            $correctOption = $userAnswer->question->answerOptions->where('is_correct', true)->first();
            $userAnswer->is_correct = $correctOption && $userAnswer->answerOption && $userAnswer->answerOption->id === $correctOption->id;
            return $userAnswer;
        });

        $totalQuestions = $userAnswers->count();
        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $score = $attempt->nilai;
        $isPassed = $attempt->is_passed === true;

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

        // Get course ID from quiz
        $courseId = $this->getCourseIdFromQuiz($attempt->quiz);
        
        if (!$courseId) {
            abort(404, 'Kursus tidak ditemukan untuk quiz ini.');
        }

        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda harus terdaftar dalam kursus ini.');
        }

        // Get detailed review
        $userAnswers = $attempt->userAnswers()
            ->with(['question.answerOptions', 'answerOption'])
            ->get();

        // Calculate is_correct for each answer (not stored in database)
        $userAnswers = $userAnswers->map(function ($userAnswer) {
            $correctOption = $userAnswer->question->answerOptions->where('is_correct', true)->first();
            $userAnswer->is_correct = $correctOption && $userAnswer->answerOption && $userAnswer->answerOption->id === $correctOption->id;
            return $userAnswer;
        });

        $totalQuestions = $userAnswers->count();
        $correctAnswers = $userAnswers->where('is_correct', true)->count();
        $score = $attempt->nilai;
        $isPassed = $attempt->is_passed === true;

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
        
        // Get course ID from quiz
        $courseId = $this->getCourseIdFromQuiz($quiz);
        
        if (!$courseId) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan untuk quiz ini.'
            ], 404);
        }
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terdaftar dalam kursus ini.'
            ], 403);
        }

        // Get active attempt (not completed yet)
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
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
        
        // Get course ID from quiz
        $courseId = $this->getCourseIdFromQuiz($quiz);
        
        if (!$courseId) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan untuk quiz ini.'
            ], 404);
        }
        
        // Check if user is enrolled (accept multiple valid enrollment statuses)
        $enrollment = $user->userEnrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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
            // Get the quiz attempt (must be in progress, i.e., not completed)
            $attempt = $user->quizAttempts()
                ->where('id', $request->attempt_id)
                ->where('quiz_id', $quiz->id)
                ->whereNull('completed_at')
                ->first();

            if (!$attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attempt quiz tidak valid.'
                ], 400);
            }

            // Save answers without calculating score
            foreach ($request->answers as $answerData) {
                $selectedOptionId = $answerData['selected_answer_option_id'] ?? null;
                
                if (!$selectedOptionId) {
                    // Skip if no answer selected
                    continue;
                }
                
                // Create or update user answer
                // UserAnswer table only has: quiz_attempt_id, question_id, answer_option_id
                UserAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $answerData['question_id']
                    ],
                    [
                        'answer_option_id' => $selectedOptionId
                    ]
                );
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
        // Check if user has reached max attempts (only count completed attempts)
        $attemptCount = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return false;
        }

        // Check if user has active attempt (not completed yet)
        $activeAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($activeAttempt) {
            return true; // Can continue existing attempt
        }

        // Check if user has passed the quiz
        $passedAttempt = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('is_passed', true)
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
                $query->where('user_id', $user->id)
                      ->where('is_passed', true)
                      ->whereNotNull('completed_at');
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

        // Check if sub-module has quiz and if user has passed it
        $subModuleQuizzes = $subModule->quizzes;
        $allSubModuleQuizzesPassed = true;
        if ($subModuleQuizzes->count() > 0) {
            foreach ($subModuleQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allSubModuleQuizzesPassed = false;
                    break;
                }
            }
        }

        if ($totalContents > 0 && $completedContents >= $totalContents && $allSubModuleQuizzesPassed) {
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

        // Check if module has quiz and if user has passed it
        $moduleQuizzes = $module->quizzes;
        $allModuleQuizzesPassed = true;
        if ($moduleQuizzes->count() > 0) {
            foreach ($moduleQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allModuleQuizzesPassed = false;
                    break;
                }
            }
        }

        if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules && $allModuleQuizzesPassed) {
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
            ->whereIn('status', ['enrolled', 'in_progress', 'completed', 'active'])
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

            // Check if module has quiz and if user has passed it
            $moduleQuizzes = $module->quizzes;
            $allModuleQuizzesPassed = true;
            if ($moduleQuizzes->count() > 0) {
                foreach ($moduleQuizzes as $quiz) {
                    if (!$quiz->hasUserPassed($user->id)) {
                        $allModuleQuizzesPassed = false;
                        break;
                    }
                }
            }

            if ($totalSubModules > 0 && $completedSubModules >= $totalSubModules && $allModuleQuizzesPassed) {
                $completedModules++;
            }
        }

        // Check if course has quiz and if user has passed it
        $courseQuizzes = $course->quizzes;
        $allCourseQuizzesPassed = true;
        if ($courseQuizzes->count() > 0) {
            foreach ($courseQuizzes as $quiz) {
                if (!$quiz->hasUserPassed($user->id)) {
                    $allCourseQuizzesPassed = false;
                    break;
                }
            }
        }

        if ($completedModules >= $totalModules && $allCourseQuizzesPassed) {
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
        // Check if JP record already exists for this course and year
        $currentYear = now()->year;
        $existingJpRecord = $user->jpRecords()
            ->where('course_id', $course->id)
            ->where('tahun', $currentYear)
            ->first();

        if (!$existingJpRecord) {
            $user->jpRecords()->create([
                'course_id' => $course->id,
                'jp_earned' => $course->jp_value ?? 0,
                'tahun' => $currentYear,
                'recorded_at' => now()
            ]);
        }
    }

    /**
     * Get course ID from quiz (quiz can be at course, module, or sub-module level).
     */
    private function getCourseIdFromQuiz(Quiz $quiz): ?int
    {
        // Load relationships if not already loaded
        if (!$quiz->relationLoaded('course')) {
            $quiz->load('course');
        }
        if (!$quiz->relationLoaded('module')) {
            $quiz->load('module');
        }
        if (!$quiz->relationLoaded('subModule')) {
            $quiz->load('subModule');
        }
        
        // Quiz can be at course, module, or sub-module level
        if ($quiz->course_id) {
            return $quiz->course_id;
        } elseif ($quiz->module_id) {
            if (!$quiz->module || !$quiz->module->relationLoaded('course')) {
                $quiz->load('module.course');
            }
            return $quiz->module->course_id ?? null;
        } elseif ($quiz->sub_module_id) {
            if (!$quiz->subModule || !$quiz->subModule->relationLoaded('module')) {
                $quiz->load('subModule.module');
            }
            if ($quiz->subModule && !$quiz->subModule->module->relationLoaded('course')) {
                $quiz->subModule->load('module.course');
            }
            return $quiz->subModule->module->course_id ?? null;
        }
        
        return null;
    }
} 