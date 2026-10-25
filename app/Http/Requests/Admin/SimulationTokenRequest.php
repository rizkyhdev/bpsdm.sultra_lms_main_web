<?php

namespace App\Http\Requests\Admin;

use App\Models\SimulationQuestion;
use App\Models\SimulationToken;
use Illuminate\Foundation\Http\FormRequest;

class SimulationTokenRequest extends FormRequest
{
    /**
     * Akses sudah dibatasi oleh middleware role:admin pada grup rute.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sesi' => ['required', 'string', 'max:100'],
            // Tidak boleh melebihi isi bank soal, karena setiap peserta mendapat soal yang berbeda-beda.
            'jumlah_soal' => ['required', 'integer', 'min:1', 'max:'.SimulationQuestion::count()],
            'durasi_menit' => ['required', 'integer', 'min:1', 'max:'.SimulationToken::MAX_DURASI_MENIT],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'integer' => ':Attribute harus berupa bilangan bulat.',
            'min' => ':Attribute minimal :min.',
            'sesi.max' => 'Nama sesi maksimal 100 karakter.',
            'jumlah_soal.max' => 'Jumlah soal tidak boleh melebihi isi bank soal (:max soal).',
            'durasi_menit.max' => 'Batas waktu maksimal :max menit.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'sesi' => 'nama sesi',
            'jumlah_soal' => 'jumlah soal',
            'durasi_menit' => 'batas waktu',
        ];
    }
}
