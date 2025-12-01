<?php

use App\Http\Controllers\Student\StudentCalendarController;
use App\Models\Course;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->controller = new StudentCalendarController();
    $this->user = User::factory()->create(['role' => 'student']);
    $this->instructor = User::factory()->create(['role' => 'instructor']);
});

test('mapCourseToEvents returns window event when both start and end are provided', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    $end = $now->addDays(12);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    $from = $now;
    $to = $now->addDays(30);
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(1);
    expect($events->first()['type'])->toBe('window');
    expect($events->first()['start_utc'])->toBe($start->toIso8601String());
    expect($events->first()['end_utc'])->toBe($end->toIso8601String());
});

test('mapCourseToEvents returns start event when only start is provided', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => null,
    ]);

    $from = $now;
    $to = $now->addDays(30);
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(1);
    expect($events->first()['type'])->toBe('start');
    expect($events->first()['start_utc'])->toBe($start->toIso8601String());
    expect($events->first()['end_utc'])->toBeNull();
});

test('mapCourseToEvents returns end event when only end is provided', function () {
    $now = CarbonImmutable::now('UTC');
    $end = $now->addDays(10);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => null,
        'end_date_time' => $end,
    ]);

    $from = $now;
    $to = $now->addDays(30);
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(1);
    expect($events->first()['type'])->toBe('end');
    expect($events->first()['start_utc'])->toBe($end->toIso8601String());
    expect($events->first()['end_utc'])->toBeNull();
});

test('mapCourseToEvents returns empty when neither start nor end are provided', function () {
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => null,
        'end_date_time' => null,
    ]);

    $now = CarbonImmutable::now('UTC');
    $from = $now;
    $to = $now->addDays(30);
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(0);
});

test('mapCourseToEvents returns empty when start is after end (invalid)', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(10);
    $end = $now->addDays(5); // Invalid: end before start
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    $from = $now;
    $to = $now->addDays(30);
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(0);
});

test('mapCourseToEvents filters events outside date range', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(50); // Outside range
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => null,
    ]);

    $from = $now;
    $to = $now->addDays(30); // Range doesn't include start
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(0);
});

test('mapCourseToEvents includes window events that span the range', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(5); // Before range
    $end = $now->addDays(10); // After range
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    $from = $now;
    $to = $now->addDays(5); // Range overlaps with window
    
    $reflection = new ReflectionClass($this->controller);
    $method = $reflection->getMethod('mapCourseToEvents');
    $method->setAccessible(true);
    
    $events = $method->invoke($this->controller, $course, $from, $to);
    
    expect($events)->toHaveCount(1);
    expect($events->first()['type'])->toBe('window');
});

