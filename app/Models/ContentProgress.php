<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentProgress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'content_id',
        'is_completed',
        'progress_percentage',
        'video_duration',
        'watched_duration',
        'current_position',
        'time_spent',
        'started_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'progress_percentage' => 'decimal:2',
            'video_duration' => 'integer',
            'watched_duration' => 'integer',
            'current_position' => 'integer',
            'time_spent' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the content that owns the progress.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
