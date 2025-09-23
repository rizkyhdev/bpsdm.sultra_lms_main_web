<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
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
            'file_path' => 'required_if:tipe,pdf,video,audio,image|file',
            'urutan' => 'required|integer',
        ];
    }
}


