<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Enums\PaymentMethodEnum;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'regex:/^(?:\+880|880|0)1[3-9][0-9]{8}$/'],
            'customer_city' => 'required|string|max:100',
            'customer_area' => 'required|string|max:100',
            'customer_address' => 'required|string|max:500',
            'transaction_number' => 'required|string|max:100',
            'payment_method' => ['required', Rule::in(PaymentMethodEnum::values())],
            'item_slugs' => 'required|array|min:1',
            'item_slugs.*' => 'required|string|exists:products,slug',
            'item_quantities' => 'required|array|min:1',
            'item_quantities.*' => 'required|integer|min:1|max:100',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name' => 'Name',
            'customer_phone' => 'Phone',
            'customer_city' => 'City',
            'customer_area' => 'Area',
            'customer_address' => 'Address',
            'transaction_number' => 'Transaction Number',
            'payment_method' => 'Payment Method',
            'item_slugs' => 'Cart Items',
            'item_quantities' => 'Cart Item Quantities',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Phone number must be a valid Bangladeshi phone number.',
            'item_slugs.*.exists' => 'Invalid product included in the cart.',
            'item_quantities.*.min' => 'Quantity must be at least 1.',
            'item_quantities.*.max' => 'Quantity must be less than 100.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => null,
            'errors' => $validator->errors(),
        ], 422));
    }
}
