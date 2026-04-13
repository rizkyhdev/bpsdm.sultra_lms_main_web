<?php
require __DIR__ . '/vendor/autoload.php';
\ = require_once __DIR__ . '/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

try {
    \ = App\Models\User::where('email', 'rizky2.student@gmail.com')->first();
    Auth::login(\);
    echo app(App\Http\Controllers\Student\StudentDashboardController::class)->index()->render();
} catch (\Exception \) {
    echo 'ERROR_STACK_TRACE: ' . \->getMessage() . '\n' . \->getTraceAsString();
}
