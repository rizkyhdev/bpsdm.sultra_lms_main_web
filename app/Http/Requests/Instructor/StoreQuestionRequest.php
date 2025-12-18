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

    /**
     * Tambahan validasi: untuk multiple_choice dan true_false
     * harus dipilih tepat satu jawaban benar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tipe = $this->input('tipe');

            if (!in_array($tipe, ['multiple_choice', 'true_false'], true)) {
                return;
            }

            $options = $this->input('answer_options', []);
            if (!is_array($options) || empty($options)) {
                return;
            }

            $correctCount = 0;
            foreach ($options as $opt) {
                if (!empty($opt['is_correct'])) {
                    $correctCount++;
                }
            }

            if ($correctCount === 0) {
                $validator->errors()->add(
                    'answer_options',
                    'Pilih minimal satu jawaban yang benar untuk pertanyaan ini.'
                );
            } elseif ($correctCount > 1) {
                $validator->errors()->add(
                    'answer_options',
                    'Pilih tepat satu jawaban yang benar untuk pertanyaan pilihan ganda / benar-salah.'
                );
            }
        });
    }
}


