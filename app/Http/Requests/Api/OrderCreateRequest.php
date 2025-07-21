<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
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
            'order_number' => 'required|numeric',
            'customer_id' => 'required|exists:customers,profy_id',
            'service_finished_at' => 'nullable',
            'price' => 'nullable|numeric',
            'type' => 'required|string|in:Cleaner,Handyman',
            'status' => 'required|string|in:CREATED,PAID,COMPLETED',
            'created_at' => 'required',
        ];
    }
}
