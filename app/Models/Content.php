<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Content extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sub_module_id',
        'judul',
        'tipe',
        'file_path',
        'html_content',
        'external_url',
        'youtube_url',
        'urutan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    /**
     * Get the sub module that owns the content.
     */
    public function subModule(): BelongsTo
    {
        return $this->belongsTo(SubModule::class);
    }

    /**
     * Get the user progress for this content.
     */
    public function userProgress()
    {
        return $this->hasMany(ContentProgress::class);
    }

    /**
     * Extract YouTube video ID from URL.
     */
    public function getYoutubeVideoIdAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        $url = trim($this->youtube_url);
        
        // Handle various YouTube URL formats
        // Standard: https://www.youtube.com/watch?v=VIDEO_ID
        // Short: https://youtu.be/VIDEO_ID
        // Embed: https://www.youtube.com/embed/VIDEO_ID
        // Mobile: https://m.youtube.com/watch?v=VIDEO_ID
        // With timestamp: https://www.youtube.com/watch?v=VIDEO_ID&t=123s
        
        $patterns = [
            // Standard watch URLs
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/',
            // Alternative patterns
            '/(?:youtube\.com\/.*[?&]v=)([a-zA-Z0-9_-]{11})/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        // If URL is just a video ID (11 characters)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }
        
        return null;
    }

    /**
     * Get YouTube embed URL.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $videoId = $this->youtube_video_id;
        if (!$videoId) {
            return null;
        }
        
        $origin = urlencode(request()->getSchemeAndHttpHost());
        return "https://www.youtube.com/embed/{$videoId}?enablejsapi=1&origin={$origin}&rel=0&modestbranding=1";
    }
} 