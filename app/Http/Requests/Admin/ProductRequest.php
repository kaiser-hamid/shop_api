<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ProductStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'size' => 'nullable|string|max:50',
            'brand_id' => 'required|numeric',
            
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|numeric',

            'featured_image' => ['required', 'file', 'image', 'max:2048'],

            'galleries' => 'required|array|min:1',
            'galleries.*' => ['required', 'file', 'image', 'max:2048'],

            'description' => 'required|string',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'nullable|string',
            'meta_description' => 'nullable|string|max:255',
            'status' => ['required', 'string', Rule::enum(ProductStatusEnum::class)],
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
