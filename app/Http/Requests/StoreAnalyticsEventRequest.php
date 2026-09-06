<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnalyticsEventRequest extends FormRequest
{
    public const TYPES = ['page_view'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(self::TYPES)],
            'path' => ['required', 'string', 'max:255', 'starts_with:/'],
            'referrer' => ['nullable', 'string', 'max:255'],
        ];
    }
}
