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
        $rules = [
            'name' => 'required|string|max:255',
            'size' => 'nullable|string|max:50',
            'brand_id' => 'required|numeric',
            
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|numeric',

            'description' => 'required|string',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'nullable|string',
            'meta_description' => 'nullable|string|max:255',
            'status' => ['required', 'string', Rule::enum(ProductStatusEnum::class)],
        ];

        if($this->isMethod('post')) {
            $rules['featured_image'] = ['required', 'file', 'image', 'max:2048'];
            $rules['galleries'] = 'required|array|min:1';
            $rules['galleries.*'] = ['required', 'file', 'image', 'max:2048'];
        } 
        
        if($this->isMethod('put')) {
            $rules['featured_image'] = ['nullable', 'file', 'image', 'max:2048'];
            $rules['galleries'] = 'nullable|array|min:1';
            $rules['galleries.*'] = ['nullable', 'file', 'image', 'max:2048'];
            $rules['galleries_exists'] = 'nullable|array';
            $rules['galleries_exists.*'] = 'nullable|string';
        }

        return $rules;
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
