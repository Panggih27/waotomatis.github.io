<?php

namespace App\Services;

use App\Jobs\SaveHistoryMessageJob;
use App\Jobs\SendMessageJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use App\Models\Tag;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendMessageService
{

    /**
     * send
     *
     * @param  Campaign $campaign
     * @return void
     */
    public static function send(Campaign $campaign)
    {

        DB::transaction(function () use ($campaign) {
            $campaign->update([
                'is_processing' => true
            ]);

            $campaign = $campaign->refresh();

            $campaign->loadMissing(['template', 'number']);

            Bus::chain([
                new SaveHistoryMessageJob($campaign),
                new SendMessageJob($campaign),
            ])->dispatch();

            return true;
        }, 3);
    }

    /**
     * validation
     *
     * @param  Campaign $campaign
     * @param  mixed $contact
     * @return void
     */
    public static function validation(Campaign $campaign, array $contact)
    {
        $campaign->loadMissing(['user.point']);

        $total = count($contact) * $campaign->point;

        if ($campaign->user->point->point < $total) {
            return false;
        }

        if (Carbon::parse($campaign->user->point->expired_at)->lt(Carbon::now())) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param  Campaign  $campaign
     * @return \Illuminate\Http\Request|string|array|null
     */
    public static function messageFormatFromArray(Campaign $campaign)
    {

        // try {
            $campaign->loadMissing(['template']);

            switch ($campaign->receivers->type) {
                case 'all':
                    $contacts = Contact::where('user_id', $campaign->user_id)->get()->map(function ($contact) {
                        return [
                            'name' => $contact->name,
                            'number' => $contact->number,
                            'var1' => $contact->var1,
                            'var2' => $contact->var2,
                            'var3' => $contact->var3,
                            'var4' => $contact->var4,
                            'var5' => $contact->var5,
                        ];
                    })->toArray();
                    break;
                case 'tag':
                    $contacts = Tag::with('contacts')->where('user_id', $campaign->user_id)->find($campaign->receivers->id)->contacts->map(function ($contact) {
                        return [
                            'name' => $contact->name,
                            'number' => $contact->number,
                            'var1' => $contact->var1,
                            'var2' => $contact->var2,
                            'var3' => $contact->var3,
                            'var4' => $contact->var4,
                            'var5' => $contact->var5,
                        ];
                    })->toArray();
                    break;
                case 'contact':
                    $contacts = collect($campaign->receivers->data)->map(function ($contact) {
                        return [
                            'name' => $contact->name,
                            'number' => $contact->number,
                            'var1' => $contact->var1,
                            'var2' => $contact->var2,
                            'var3' => $contact->var3,
                            'var4' => $contact->var4,
                            'var5' => $contact->var5,
                        ];
                    })->toArray();
                    break;
                case 'group':
                    $group = Group::find($campaign->receivers->id);
                    if ($campaign->receivers->is_broadcast) {
                        $groups = Http::asForm()->get(env('WA_URL_SERVER') . '/metadata-group/' . $campaign->number->body . '/' . $group->jid)->json();
                        $contacts = collect($groups['data']['participants'])->where('id', '<>', $campaign->number->body . '@s.whatsapp.net')->map(function ($contact) {
                            return [
                                'name' => '',
                                'number' => explode('@', $contact['id'])[0],
                                'var1' => '',
                                'var2' => '',
                                'var3' => '',
                                'var4' => '',
                                'var5' => '',
                            ];
                        })->toArray();
                    } else {
                        $contacts[] = [
                            'name' => '',
                            'number' => $group->jid . '@g.us',
                            'var1' => '',
                            'var2' => '',
                            'var3' => '',
                            'var4' => '',
                            'var5' => '',
                        ];
                    }
                    break;
                case 'random':
                    $contacts = Contact::where('user_id', $campaign->user_id)->inRandomOrder()->take($campaign->receivers->id)->get()->map(function ($contact) {
                        return [
                            'name' => $contact->name,
                            'number' => $contact->number,
                            'var1' => $contact->var1,
                            'var2' => $contact->var2,
                            'var3' => $contact->var3,
                            'var4' => $contact->var4,
                            'var5' => $contact->var5,
                        ];
                    })->toArray();
                    break;
                default:
                    return [
                        'status' => false,
                        'message' => 'Wrong Receiver Type',
                        'data' => []
                    ];
                    break;
            }

            if (!static::validation($campaign, $contacts)) {
                return [
                    'status' => false,
                    'message' => 'Your Point Was Not Enough or Your Point is Expired To Send This Campaign',
                    'data' => []
                ];
            }

            $variable = ["{name}", "{var1}", "{var2}", "{var3}", "{var4}", "{var5}"];

            if (!is_null($campaign->template->template)) {
                
                $data['footer'] = $campaign->template->template->footer;
                $data['data'] = collect($campaign->template->template->templateButtons)->map(function($t) {
                    return [
                        'type' => ($t->type == 'url' ? 'urlButton' : 'callButton'),
                        'text' => $t->displayText,
                        'action' => ($t->type == 'url' ? '' : '+') . $t->action
                    ];
                });

                foreach ($contacts as $contact) {
                    $value = [$contact["name"], $contact["var1"], $contact["var2"], $contact["var3"], $contact["var4"], $contact["var5"]];
                    
                    $data['caption'] = extractWord($campaign->template->template->text, str_replace($variable, $value, $campaign->template->template->text));
                    $result['template'][] = [
                        'type' => 'template',
                        'sender' => $campaign->number->body,
                        'receiver' => $contact['number'],
                        'data' => $data
                    ];
                }

                $data = [];
            }

            if (!is_null($campaign->template->media)) {
                $data['url'] = $campaign->template->media->url;

                foreach ($contacts as $contact) {
                    $value = [$contact["name"], $contact["var1"], $contact["var2"], $contact["var3"], $contact["var4"], $contact["var5"]];
                    $data['caption'] = str_replace($variable, $value, $campaign->template->media->caption);
                    $result['media'][] = [
                        'type' => 'media',
                        'sender' => $campaign->number->body,
                        'receiver' => $contact['number'],
                        'data' => $data
                    ];
                }

                $data = [];
            }

            if (!is_null($campaign->template->button)) {
                $data['footer'] = $campaign->template->button->footer;
                $data['data'] = collect($campaign->template->button->buttons)->map(function ($button) {
                    return $button->displayText;
                })->toArray();

                foreach ($contacts as $contact) {
                    $value = [$contact["name"], $contact["var1"], $contact["var2"], $contact["var3"], $contact["var4"], $contact["var5"]];
                    $data['caption'] = str_replace($variable, $value, $campaign->template->button->text);
                    $result['button'][] = [
                        'type' => 'button',
                        'sender' => $campaign->number->body,
                        'receiver' => $contact['number'],
                        'data' => $data
                    ];
                }

                $data = [];
            }

            if (!is_null($campaign->template->contact)) {

                foreach ($contacts as $contact) {
                    $result['contact'][] = [
                        'type' => 'contact',
                        'sender' => $campaign->number->body,
                        'receiver' => $contact['number'],
                        'data' => $campaign->template->contact
                    ];
                }

                $data = [];
            }

            if (!is_null($campaign->template->text)) {
                foreach ($contacts as $contact) {
                    $value = [$contact["name"], $contact["var1"], $contact["var2"], $contact["var3"], $contact["var4"], $contact["var5"]];
                    $data['text'] = extractWord($campaign->template->text, str_replace($variable, $value, $campaign->template->text));
                    $result['text'][] = [
                        'type' => 'text',
                        'sender' => $campaign->number->body,
                        'receiver' => $contact['number'],
                        'data' => $data
                    ];
                }
            }

            if (!is_null($campaign->template->location)) {
                $data['lat'] = $campaign->template->location->lat;
                $data['long'] = $campaign->template->location->long;;

                foreach ($contacts as $contact) {
                    $result['location'][] = [
                        'type' => 'location',
                        'sender' => $campaign->number->body,
                        'receiver' => $contact['number'],
                        'data' => $data
                    ];
                }

                $data = [];
            }

            return [
                'status' => true,
                'message' => 'Success',
                'data' => $result
            ];
        // } catch (Exception $error) {
        //     return [
        //         'status' => false,
        //         'message' => $error->getMessage(),
        //         'data' => []
        //     ];
        // }
    }
}
