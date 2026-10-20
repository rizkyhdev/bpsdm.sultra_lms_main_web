<?php

namespace App\Http\Requests\Admin;

use App\Models\SimulationQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimulationQuestionRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware role:admin pada grup rute.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kunci_jawaban' => strtoupper(trim((string) $this->input('kunci_jawaban'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pertanyaan' => ['required', 'string', 'max:5000'],
            'pilihan_a' => ['required', 'string', 'max:2000'],
            'pilihan_b' => ['required', 'string', 'max:2000'],
            'pilihan_c' => ['required', 'string', 'max:2000'],
            'pilihan_d' => ['required', 'string', 'max:2000'],
            'kunci_jawaban' => ['required', Rule::in(SimulationQuestion::OPTIONS)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'max' => ':Attribute maksimal :max karakter.',
            'kunci_jawaban.required' => 'Kunci jawaban wajib dipilih.',
            'kunci_jawaban.in' => 'Kunci jawaban harus salah satu dari A, B, C, atau D.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pertanyaan' => 'pertanyaan',
            'pilihan_a' => 'pilihan A',
            'pilihan_b' => 'pilihan B',
            'pilihan_c' => 'pilihan C',
            'pilihan_d' => 'pilihan D',
            'kunci_jawaban' => 'kunci jawaban',
        ];
    }
}
