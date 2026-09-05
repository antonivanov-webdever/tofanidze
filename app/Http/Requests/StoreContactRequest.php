<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'company' => ['nullable', 'string', 'max:120'],
            'budget' => ['nullable', 'string', 'max:60'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],

            // Honeypot: a real visitor never sees this field, bots fill everything.
            'website' => ['nullable', 'size:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.min' => 'A little more detail helps me give you a useful answer.',
            'website.size' => 'Your submission looked automated. Please try again.',
        ];
    }

    public function attributes(): array
    {
        return [
            'message' => 'project details',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Bots typically post instantly; humans need a few seconds to type.
                $renderedAt = (int) $this->input('rendered_at');

                if ($renderedAt > 0 && (time() - $renderedAt) < 3) {
                    $validator->errors()->add('message', 'Your submission looked automated. Please try again.');
                }
            },
        ];
    }

    public function payload(): array
    {
        return array_merge(
            $this->safe()->except('website'),
            [
                'ip_address' => $this->ip(),
                'user_agent' => substr((string) $this->userAgent(), 0, 255),
            ]
        );
    }
}
