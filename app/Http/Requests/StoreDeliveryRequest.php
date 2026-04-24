<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_company' => ['required', 'string', 'max:255'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'driver' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'fuel_id' => ['required', 'integer', 'exists:fuels,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ];
    }
}
