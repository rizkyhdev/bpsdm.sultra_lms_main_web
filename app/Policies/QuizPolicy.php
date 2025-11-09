<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    /**
     * Determine if the user can view the quiz.
     */
    public function view(User $user, Quiz $quiz)
    {
        // Admins can view all quizzes
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructors can only view quizzes in their own courses
        if ($user->role === 'instructor') {
            // Get course based on quiz level
            $course = null;
            if ($quiz->sub_module_id) {
                $course = $quiz->subModule->module->course;
            } elseif ($quiz->module_id) {
                $course = $quiz->module->course;
            } elseif ($quiz->course_id) {
                $course = $quiz->course;
            }
            
            if ($course) {
                return (int) $course->user_id === (int) $user->id;
            }
        }
        
        return false;
    }

    /**
     * Determine if the user can create quizzes.
     */
    public function create(User $user, $subModule = null)
    {
        // Admins and instructors can create quizzes
        // If subModule is provided, we could add additional checks here
        return in_array($user->role, ['admin', 'instructor']);
    }

    /**
     * Determine if the user can update the quiz.
     */
    public function update(User $user, Quiz $quiz)
    {
        // Admins can update all quizzes
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructors can only update quizzes in their own courses
        if ($user->role === 'instructor') {
            // Get course based on quiz level
            $course = null;
            if ($quiz->sub_module_id) {
                $course = $quiz->subModule->module->course;
            } elseif ($quiz->module_id) {
                $course = $quiz->module->course;
            } elseif ($quiz->course_id) {
                $course = $quiz->course;
            }
            
            if ($course) {
                return (int) $course->user_id === (int) $user->id;
            }
        }
        
        return false;
    }

    /**
     * Determine if the user can delete the quiz.
     */
    public function delete(User $user, Quiz $quiz)
    {
        // Admins can delete all quizzes
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructors can only delete quizzes in their own courses
        if ($user->role === 'instructor') {
            // Get course based on quiz level
            $course = null;
            if ($quiz->sub_module_id) {
                $course = $quiz->subModule->module->course;
            } elseif ($quiz->module_id) {
                $course = $quiz->module->course;
            } elseif ($quiz->course_id) {
                $course = $quiz->course;
            }
            
            if ($course) {
                return (int) $course->user_id === (int) $user->id;
            }
        }
        
        return false;
    }
}


