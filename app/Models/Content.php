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

        $url = $this->youtube_url;
        
        // Handle various YouTube URL formats
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return $matches[1];
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
        
        return "https://www.youtube.com/embed/{$videoId}?enablejsapi=1&origin=" . urlencode(request()->getSchemeAndHttpHost());
    }
} 