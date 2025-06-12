<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreateRequest extends FormRequest
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
            'profy_id' => 'required|integer|unique:customers,profy_id',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'allow_notification' => 'nullable|boolean',
            'onesignal_player_id' => 'nullable|string',
        ];
    }
}
