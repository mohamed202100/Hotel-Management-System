<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer')->id ?? null;

        return [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('customers', 'email')->ignore($customerId), 'max:255'],
            'phone_number' => 'required|string|max:20|min:10',
            'passport_id'  => ['required', 'string', Rule::unique('customers', 'passport_id')->ignore($customerId), 'max:20', 'min:14'],
        ];
    }
}
