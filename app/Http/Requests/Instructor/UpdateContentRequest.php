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
            'tipe' => 'required|in:text,pdf,video,audio,image',
            'file_path' => 'nullable|file',
            'urutan' => 'required|integer',
        ];
    }
}


