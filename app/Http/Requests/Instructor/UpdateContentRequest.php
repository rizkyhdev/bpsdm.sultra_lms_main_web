<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:text,html,pdf,video,audio,image,link,youtube',
            'file_path' => 'nullable|file|max:102400', // Max 100MB
            'html_content' => 'nullable|string|required_if:tipe,html,text',
            'external_url' => 'nullable|url|required_if:tipe,link',
            'youtube_url' => 'nullable|url|required_if:tipe,youtube',
            'required_duration' => 'nullable|integer|min:1',
            'urutan' => 'required|integer|min:1',
        ];
    }
    
    public function messages()
    {
        return [
            'html_content.required_if' => 'HTML content is required for HTML and text content types.',
            'external_url.required_if' => 'External URL is required for link content type.',
            'external_url.url' => 'External URL must be a valid URL.',
            'youtube_url.required_if' => 'YouTube URL is required for YouTube content type.',
            'youtube_url.url' => 'YouTube URL must be a valid URL.',
            'required_duration.integer' => 'Required duration must be a number.',
            'required_duration.min' => 'Required duration must be at least 1 second.',
        ];
    }
}


