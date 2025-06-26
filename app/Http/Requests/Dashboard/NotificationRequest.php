<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'trigger' => 'required|string',
            'notification_type' => 'required|array',
            'notification_type.*' => 'required|string',
            'email_template' => 'nullable|string',
            'active' => 'nullable|boolean',
            'criterion' => 'nullable|array',
            'criterion.*.type' => 'required|string',
            'criterion.*.additional' => 'nullable',
            'category_id' => 'nullable|exists:notification_categories,id',
            'additional' => 'nullable|array',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.subject' => 'required|string',
            'translations.*.content' => 'required|string',
        ];
    }
}
