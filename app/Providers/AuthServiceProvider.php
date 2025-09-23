<?php

namespace App\Providers;

use App\Models\Content;
use App\Models\Course;
use App\Models\Module;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SubModule;
use App\Models\UserEnrollment;
use App\Policies\ContentPolicy;
use App\Policies\CoursePolicy;
use App\Policies\ModulePolicy;
use App\Policies\QuestionPolicy;
use App\Policies\QuizAttemptPolicy;
use App\Policies\QuizPolicy;
use App\Policies\SubModulePolicy;
use App\Policies\UserEnrollmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Course::class => CoursePolicy::class,
        Module::class => ModulePolicy::class,
        SubModule::class => SubModulePolicy::class,
        Content::class => ContentPolicy::class,
        Quiz::class => QuizPolicy::class,
        Question::class => QuestionPolicy::class,
        UserEnrollment::class => UserEnrollmentPolicy::class,
        QuizAttempt::class => QuizAttemptPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}


