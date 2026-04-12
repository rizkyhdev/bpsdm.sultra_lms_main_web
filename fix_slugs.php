<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use Illuminate\Support\Str;

$courses = Course::whereNull('slug')->get();
foreach ($courses as $c) {
    $c->slug = Str::slug($c->judul);
    $c->save();
}

echo "Updated " . $courses->count() . " courses.\n";
