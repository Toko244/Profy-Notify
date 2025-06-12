<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisteredRequest extends FormRequest
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
            'id' => 'required|integer',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:15',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'disable_push_notification' => 'nullable|boolean',
            'player_id' => 'nullable|string|max:255',
        ];
    }
}
