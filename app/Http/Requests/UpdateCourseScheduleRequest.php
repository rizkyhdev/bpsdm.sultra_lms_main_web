<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date_time' => [
                'nullable',
                'date',
                'before:end_date_time',
            ],
            'end_date_time' => [
                'nullable',
                'date',
                'after:start_date_time',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_date_time.before' => __('The start date must be before the end date.'),
            'end_date_time.after' => __('The end date must be after the start date.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert local datetime to UTC if provided
        if ($this->has('start_date_time') && $this->start_date_time) {
            $this->merge([
                'start_date_time' => $this->convertToUtc($this->start_date_time),
            ]);
        }

        if ($this->has('end_date_time') && $this->end_date_time) {
            $this->merge([
                'end_date_time' => $this->convertToUtc($this->end_date_time),
            ]);
        }
    }

    /**
     * Convert datetime string to UTC.
     */
    private function convertToUtc(string $dateTime): string
    {
        try {
            return \Carbon\Carbon::parse($dateTime, config('app.timezone'))
                ->setTimezone('UTC')
                ->toDateTimeString();
        } catch (\Exception $e) {
            // If parsing fails, return as-is and let validation handle it
            return $dateTime;
        }
    }
}
