<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'filename' => ['required', 'string', 'max:255'],
            'candidate_name' => ['required', 'string', 'max:255'],
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
