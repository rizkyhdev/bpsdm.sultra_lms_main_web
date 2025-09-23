<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan namespace komponen anonim Blade untuk area Student
        // Sehingga dapat digunakan sebagai <x-student::input>, <x-student::card>, dll
        Blade::anonymousComponentPath(resource_path('views/student/components'), 'student');
    }
}
