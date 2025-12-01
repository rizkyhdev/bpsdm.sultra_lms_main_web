<?php

use App\Models\Course;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->instructor = User::factory()->create(['role' => 'instructor']);
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->student = User::factory()->create(['role' => 'student']);
    $this->course = Course::factory()->create(['user_id' => $this->instructor->id]);
});

test('course schedule status returns BEFORE_START when before start time', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(1);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $start->addDays(7),
    ]);
    
    expect($this->course->scheduleStatus($now))
        ->toBe(Course::SCHEDULE_STATUS_BEFORE_START);
});

test('course schedule status returns IN_PROGRESS when between start and end', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(1);
    $end = $now->addDays(6);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);
    
    expect($this->course->scheduleStatus($now))
        ->toBe(Course::SCHEDULE_STATUS_IN_PROGRESS);
});

test('course schedule status returns AFTER_END when after end time', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(8);
    $end = $now->subDays(1);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);
    
    expect($this->course->scheduleStatus($now))
        ->toBe(Course::SCHEDULE_STATUS_AFTER_END);
});

test('course schedule status returns ALWAYS_OPEN when no dates set', function () {
    expect($this->course->scheduleStatus())
        ->toBe(Course::SCHEDULE_STATUS_ALWAYS_OPEN);
});

test('canEnroll returns true when IN_PROGRESS', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(1);
    $end = $now->addDays(6);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);
    
    expect($this->course->canEnroll($now))->toBeTrue();
});

test('canEnroll returns false when BEFORE_START', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(1);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $start->addDays(7),
    ]);
    
    expect($this->course->canEnroll($now))->toBeFalse();
});

test('canEnroll returns false when AFTER_END', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(8);
    $end = $now->subDays(1);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);
    
    expect($this->course->canEnroll($now))->toBeFalse();
});

test('instructor can update schedule for own course', function () {
    $response = $this->actingAs($this->instructor)
        ->patchJson(route('courses.schedule.update', $this->course), [
            'start_date_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date_time' => now()->addDays(8)->format('Y-m-d H:i:s'),
        ]);
    
    $response->assertOk();
    $response->assertJson(['success' => true]);
});

test('admin can update schedule for any course', function () {
    $response = $this->actingAs($this->admin)
        ->patchJson(route('courses.schedule.update', $this->course), [
            'start_date_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date_time' => now()->addDays(8)->format('Y-m-d H:i:s'),
        ]);
    
    $response->assertOk();
    $response->assertJson(['success' => true]);
});

test('student cannot update schedule', function () {
    $response = $this->actingAs($this->student)
        ->patchJson(route('courses.schedule.update', $this->course), [
            'start_date_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date_time' => now()->addDays(8)->format('Y-m-d H:i:s'),
        ]);
    
    $response->assertForbidden();
});

test('schedule update validates start before end', function () {
    $response = $this->actingAs($this->instructor)
        ->patchJson(route('courses.schedule.update', $this->course), [
            'start_date_time' => now()->addDays(8)->format('Y-m-d H:i:s'),
            'end_date_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ]);
    
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['start_date_time', 'end_date_time']);
});

test('enrollment is blocked when before start', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->addDays(1);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $start->addDays(7),
    ]);
    
    $response = $this->actingAs($this->student)
        ->post(route('student.enroll', $this->course));
    
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('enrollment is blocked when after end', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(8);
    $end = $now->subDays(1);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);
    
    $response = $this->actingAs($this->student)
        ->post(route('student.enroll', $this->course));
    
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('enrollment is allowed when in progress', function () {
    $now = CarbonImmutable::now('UTC');
    $start = $now->subDays(1);
    $end = $now->addDays(6);
    
    $this->course->update([
        'start_date_time' => $start,
        'end_date_time' => $end,
    ]);
    
    $response = $this->actingAs($this->student)
        ->post(route('student.enroll', $this->course));
    
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

