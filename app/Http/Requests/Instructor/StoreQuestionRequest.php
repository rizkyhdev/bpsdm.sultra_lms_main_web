<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:multiple_choice,true_false,essay',
            'bobot' => 'required|integer|min:1',
            'urutan' => 'nullable|integer|min:1',
            'answer_options' => 'required_if:tipe,multiple_choice,true_false|array|min:2',
            'answer_options.*.teks_jawaban' => 'required|string',
            'answer_options.*.is_correct' => 'boolean',
        ];
    }
}


