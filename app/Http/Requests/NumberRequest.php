<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NumberRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'sender' => ['required', 'min:10', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/', Rule::unique('numbers', 'body')->ignore($this->device)],
            'delay_type' => ['required', 'in:time,random'],
            'delay' => ['required_if:delay_type,time', 'nullable', 'integer', 'min:1'],
            'delay_from' => ['required_if:delay_type,random', 'nullable',  'integer', 'min:1'],
            'delay_to' => ['required_if:delay_type,random', 'nullable',  'integer', 'min:2', 'gte:delay_from'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i', 'after:start'],
            'active' => ['boolean'],
        ];
    }
}
