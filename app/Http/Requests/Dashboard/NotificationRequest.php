<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
{
    protected bool $requiresSubject = false;

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $types = $this->input('notification_type', []);
        $this->requiresSubject = in_array('email', $types) || in_array('push', $types);
    }

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
     * @return array
     */
    public function rules(): array
    {
        $rules = [
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
            'translations.*.content' => 'required|string',
        ];

        if ($this->requiresSubject) {
            $rules['translations.*.subject'] = 'required|string';
        } else {
            $rules['translations.*.subject'] = 'nullable|string';
        }

        return $rules;
    }
}
