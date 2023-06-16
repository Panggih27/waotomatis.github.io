<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutoReplyRequest extends FormRequest
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
            'sender' => ['required', 'uuid', Rule::exists('numbers', 'id')->where('user_id', auth()->id())],
            'keyword' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9@;:-]*$/', Rule::unique('autoreplies', 'keyword')->where('user_id', auth()->id())->ignore($this->autoreply)],
            'search' => ['required', 'string', 'in:first,last,contains,exact'],
            'type' => ['required', 'string', 'in:text,template,media,button,location,contact'],
            'caption' => ['required_unless:type,location', 'string', 'max:255'],

            'media' => ['required_if:type,media', 'url'],

            'footer' => ['required_if:type,button', 'required_if:type,template', 'string', 'max:255'],

            'button-type' => ['required_with:caption_template', 'in:url,call'],
            'text' => ['required_with:caption_template', 'string', 'max:15'],
            'action' => ['required_with:caption_template', Rule::when(request('button-type') == 'url', ['url', 'string']), Rule::when(request('button-type') == 'call', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/')],

            'button-type2' => ['sometimes', 'required_with:caption_template', 'in:url,call'],
            'text2' => ['sometimes', 'required_with:button-type2', 'string', 'max:15'],
            'action2' => ['sometimes', 'required_with:button-type2', Rule::when(request('button-type2') == 'url', ['url', 'string']), Rule::when(request('button-type2') == 'call', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/')],

            'button1' => ['required_if:type,button', 'string', 'max:15'],
            'button2' => ['nullable', 'string', 'max:15'],
            'button3' => ['nullable', 'string', 'max:15'],

            'latitude' => ['required_if:type,location', 'string', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'longitude' => ['required_if:type,location', 'string', 'regex:/^(-?\d+(\.\d+)?)$/'],
        ];
    }
}
