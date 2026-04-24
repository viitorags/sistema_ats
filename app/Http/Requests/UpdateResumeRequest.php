<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'filename' => ['sometimes', 'required', 'string', 'max:255'],
            'candidate_name' => ['sometimes', 'required', 'string', 'max:255'],
            'candidate_email' => ['nullable', 'string', 'email', 'max:255'],
            'candidate_phone' => ['nullable', 'string', 'max:255'],
            'score' => ['nullable', 'integer'],
            'technical_score' => ['nullable', 'integer'],
            'match_score' => ['nullable', 'integer'],
            'summary' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'category' => ['nullable', 'string', 'max:255'],
            'processing_time_ms' => ['nullable', 'integer'],
        ];
    }
}
