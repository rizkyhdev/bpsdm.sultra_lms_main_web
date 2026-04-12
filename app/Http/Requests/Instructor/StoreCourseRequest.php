<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jp_value' => 'required|integer|min:1',
            'bidang_kompetensi' => 'required|string',
            'start_date_time' => 'nullable|date',
            'end_date_time' => 'nullable|date|after:start_date_time',
        ];
    }
}


