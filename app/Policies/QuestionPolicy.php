<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Get the course from a quiz (handles course, module, and sub-module levels).
     */
    private function getCourseFromQuiz(Quiz $quiz)
    {
        if ($quiz->sub_module_id) {
            return $quiz->subModule->module->course ?? null;
        } elseif ($quiz->module_id) {
            return $quiz->module->course ?? null;
        } elseif ($quiz->course_id) {
            return $quiz->course ?? null;
        }
        return null;
    }

    /**
     * Determine if the user can view the question.
     */
    public function view(User $user, Question $question)
    {
        // Admins can view all questions
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructors can only view questions in their own courses
        if ($user->role === 'instructor') {
            // Load quiz relationships if not already loaded
            if (!$question->relationLoaded('quiz')) {
                $question->load('quiz');
            }
            
            $quiz = $question->quiz;
            
            // Load quiz relationships based on quiz level
            if ($quiz->sub_module_id && !$quiz->relationLoaded('subModule')) {
                $quiz->load('subModule.module.course');
            } elseif ($quiz->module_id && !$quiz->relationLoaded('module')) {
                $quiz->load('module.course');
            } elseif ($quiz->course_id && !$quiz->relationLoaded('course')) {
                $quiz->load('course');
            }
            
            $course = $this->getCourseFromQuiz($quiz);
            
            if ($course) {
                return (int) $course->user_id === (int) $user->id;
            }
        }
        
        return false;
    }

    /**
     * Determine if the user can create questions.
     */
    public function create(User $user, Quiz $quiz = null)
    {
        // Admins can create questions
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructors can create questions in their own courses
        if ($user->role === 'instructor' && $quiz) {
            // Load quiz relationships based on quiz level
            if ($quiz->sub_module_id && !$quiz->relationLoaded('subModule')) {
                $quiz->load('subModule.module.course');
            } elseif ($quiz->module_id && !$quiz->relationLoaded('module')) {
                $quiz->load('module.course');
            } elseif ($quiz->course_id && !$quiz->relationLoaded('course')) {
                $quiz->load('course');
            }
            
            $course = $this->getCourseFromQuiz($quiz);
            
            if ($course) {
                return (int) $course->user_id === (int) $user->id;
            }
        }
        
        return false;
    }

    /**
     * Determine if the user can update the question.
     */
    public function update(User $user, Question $question)
    {
        return $this->view($user, $question);
    }

    /**
     * Determine if the user can delete the question.
     */
    public function delete(User $user, Question $question)
    {
        return $this->view($user, $question);
    }
}


