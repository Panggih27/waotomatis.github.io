<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('products', 'title')->ignore($this->product)],
            'description' => ['required', 'string', 'max:255'],
            'image' => [Rule::when(request()->isMethod('PATCH'), 'nullable', 'required'), 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'price' => ['required', 'integer'],
            'point' => ['required', 'integer'],
            'duration' => ['required', 'integer'],
            'discount_type' => ['nullable', 'string', 'in:percentage,amount'],
            'discount' => [
                'nullable',
                Rule::when(request('discount_type') == 'presentase', ['regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'min:0', 'max:95']),
                Rule::when(request('discount_type') == 'nominal', ['integer', 'min:1'])
            ],
        ];
    }
}
