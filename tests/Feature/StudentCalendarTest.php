<?php

use App\Models\Course;
use App\Models\User;
use App\Models\UserEnrollment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->student = User::factory()->create(['role' => 'student']);
    $this->instructor = User::factory()->create(['role' => 'instructor']);
    $this->otherStudent = User::factory()->create(['role' => 'student']);
});

test('student can access calendar page', function () {
    $response = $this->actingAs($this->student)
        ->get(route('student.calendar.index'));
    
    $response->assertOk();
    $response->assertViewIs('student.calendar.index');
});

test('unauthenticated user cannot access calendar page', function () {
    $response = $this->get(route('student.calendar.index'));
    
    $response->assertRedirect(route('login'));
});

test('student can fetch calendar events via API', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    $end = $now->addDays(12);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertOk();
    $response->assertJsonStructure([
        'events' => [
            '*' => [
                'id',
                'course_id',
                'title',
                'start_utc',
                'end_utc',
                'type',
                'is_enrolled',
            ],
        ],
        'from',
        'to',
    ]);
    
    expect($response->json('events'))->toHaveCount(1);
    expect($response->json('events.0.type'))->toBe('window');
});

test('calendar API requires date range', function () {
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar'));
    
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['from', 'to']);
});

test('calendar API validates date range (to must be after from)', function () {
    $now = CarbonImmutable::now('UTC');
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $now->addDays(10)->toIso8601String(),
            'to' => $now->toIso8601String(), // Invalid: to before from
        ]));
    
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['to']);
});

test('calendar API returns events for courses with start only', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => null,
    ]);

    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertOk();
    expect($response->json('events'))->toHaveCount(1);
    expect($response->json('events.0.type'))->toBe('start');
});

test('calendar API returns events for courses with end only', function () {
    $now = CarbonImmutable::now('UTC');
    $end = $now->addDays(10);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => null,
        'end_date_time' => $end,
    ]);

    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertOk();
    expect($response->json('events'))->toHaveCount(1);
    expect($response->json('events.0.type'))->toBe('end');
});

test('calendar API excludes courses with neither start nor end', function () {
    Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => null,
        'end_date_time' => null,
    ]);

    $now = CarbonImmutable::now('UTC');
    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertOk();
    expect($response->json('events'))->toHaveCount(0);
});

test('calendar API marks enrolled courses correctly', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    $end = $now->addDays(12);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    // Enroll student
    UserEnrollment::factory()->create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertOk();
    expect($response->json('events.0.is_enrolled'))->toBeTrue();
});

test('calendar API does not show enrolled status for non-enrolled courses', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    $end = $now->addDays(12);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    // Don't enroll student

    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertOk();
    expect($response->json('events.0.is_enrolled'))->toBeFalse();
});

test('calendar API caches results', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(5);
    $end = $now->addDays(12);
    
    $course = Course::factory()->create([
        'user_id' => $this->instructor->id,
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);

    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    // First request
    $response1 = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response1->assertOk();
    
    // Second request should use cache
    $response2 = $this->actingAs($this->student)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response2->assertOk();
    expect($response2->json('events'))->toHaveCount(1);
});

test('non-student cannot access calendar API', function () {
    $instructor = User::factory()->create(['role' => 'instructor']);
    
    $now = CarbonImmutable::now('UTC');
    $from = $now->toIso8601String();
    $to = $now->addDays(30)->toIso8601String();
    
    $response = $this->actingAs($instructor)
        ->getJson(route('api.student.calendar', [
            'from' => $from,
            'to' => $to,
        ]));
    
    $response->assertForbidden();
});

