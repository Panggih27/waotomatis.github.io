<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'receiver' => ['required', 'string', 'in:contact,tag,group,random,all'],

            'numbers' => ['required_if:receiver,contact', 'array'],
            'numbers.*' => ['required_if:receiver,contact', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/'],
            'tag' => ['required_if:receiver,tag', 'exists:tags,id'],
            'random' => ['required_if:receiver,random', 'integer', 'min:1'],
            'group_type' => ['required_if:receiver,group', 'in:direct,broadcast'],
            'group_list' => ['required_if:receiver,group', 'exists:groups,id'],

            'text-campaign' => ['sometimes', 'required', 'string'],
            'template-campaign' => ['sometimes', 'required', 'string'],
            'media-campaign' => ['sometimes', 'required', 'string'],
            'button-campaign' => ['sometimes', 'required', 'string'],
            'contact-campaign' => ['sometimes', 'required', 'string'],
            'location-campaign' => ['sometimes', 'required', 'string'],
            // 'type' => ['required', 'in:template,media,button,none'],

            'message' => ['required_with:text-campaign', 'string'],

            // 'switch_template' => ['sometimes', 'in:1', 'string'],
            'caption_template' => ['required_with:template-campaign', 'string'],
            'button-type' => ['required_with:template-campaign', 'in:url,call'],
            'text' => ['required_with:template-campaign', 'string', 'max:15'],
            'action' => ['required_with:template-campaign', Rule::when(request('button-type') == 'url', ['url', 'string']), Rule::when(request('button-type') == 'call', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/')],

            'button-type2' => ['sometimes', 'required_with:template-campaign', 'in:url,call'],
            'text2' => ['sometimes', 'required_with:template-campaign', 'string', 'max:15'],
            'action2' => ['sometimes', 'required_with:template-campaign', Rule::when(request('button-type2') == 'url', ['url', 'string']), Rule::when(request('button-type2') == 'call', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/')],

            // 'switch_media' => ['sometimes', 'in:1', 'string'],
            'caption_media' => ['required_with:media-campaign', 'string'],
            'media' => ['required_with:media-campaign', 'url'],

            // 'switch_button' => ['sometimes', 'in:1', 'string'],
            'caption_button' => ['required_with:button-campaign', 'string'],
            'button1' => ['required_with:button-campaign', 'string', 'max:15'],
            'button2' => ['nullable', 'string', 'max:15'],
            'button3' => ['nullable', 'string', 'max:15'],

            'footer_template' => ['required_with:template-campaign', 'string', 'max:255'],
            'footer_button' => ['required_with:button-campaign', 'string', 'max:255'],

            'scheduling' => ['required', 'in:schedule,0,now'],
            'schedule' => ['required_if:scheduling,schedule', 'after:now', 'date'],

            // 'contact-name' => ['required_with:contact-campaign', 'string', 'max:50'],
            // 'contact-fname' => ['nullable', 'string', 'max:50'],
            // 'org' => ['nullable', 'string', 'max:25'],
            'vcard' => ['required_with:contact-campaign', 'array'],
            'vcard.*' => ['required_with:contact-campaign', 'string', Rule::exists('contacts', 'number')->where('user_id', auth()->id())],

            'latitude' => ['required_with:location-campaign', 'string', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'longitude' => ['required_with:location-campaign', 'string', 'regex:/^(-?\d+(\.\d+)?)$/']
        ];
    }
}
