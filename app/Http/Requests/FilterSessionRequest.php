<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\SessionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for filtering session data.
 */
class FilterSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'session_type' => ['sometimes', 'string', Rule::in(SessionType::values())],
            'driver_id' => ['sometimes', 'uuid', 'exists:drivers,id'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'fastest_only' => ['sometimes', 'boolean'],
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
            'session_type.in' => 'Invalid session type. Must be one of: ' . implode(', ', SessionType::values()),
            'driver_id.exists' => 'The selected driver does not exist.',
            'limit.max' => 'Maximum limit is 1000 records.',
        ];
    }
}
