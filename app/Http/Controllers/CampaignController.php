<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Jobs\SaveHistoryMessageJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Cost;
use App\Models\Group;
use App\Models\Number;
use App\Models\Tag;
use App\Services\SendMessageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.campaign.index', [
            'campaigns' => Campaign::with('number')->whereBelongsTo(auth()->user())->latest()->get(),
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $campaign
     * @return void
     */
    public function show(Campaign $campaign)
    {
        return view('pages.campaign.detail', [
            'campaign' => $campaign->loadMissing(['template', 'number']),
            'recipient' => $campaign->messages()->count(),
            'success' => $campaign->messages()->where('status', 'success')->count(),
            'pending' => $campaign->messages()->where('status', 'pending')->count(),
            'failed' => $campaign->messages()->where('status', 'failed')->count(),
            'broadcast_point' => $campaign->broadcast_point,
        ]);
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('pages.campaign.updateOrCreate', [
            'senders' => Number::whereBelongsTo(auth()->user())->get(),
            'cost' => Cost::all()
        ]);
    }
        
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(CampaignRequest $request)
    {
        $data = $request->validated();

        $result = static::processingFormat($request);

        $campaign = Campaign::create([
            'user_id' => auth()->id(),
            'number_id' => $data['sender'],
            'title' => $data['title'],
            'slug' => $data['title'],
            'receivers' => $result['receivers'],
            'point' => $result['point'] ?? 0,
            'schedule' => $data['schedule'] ?? null,
            'is_manual' => $request->has('schedule') ? 0 : ($data['scheduling'] == '0' ? 1 : 0),
        ]);

        $campaign->template()->create($result['result']);

        if ($data['scheduling'] == 'now') {
            SaveHistoryMessageJob::dispatchSync($campaign);

            $process = static::send(['campaign' => $campaign->id]);
            $res = json_decode($process);

            if (isset($res->status) && $res->status) {
                $campaign->update([
                    'executed_at' => now(),
                    'is_processing' => 1,
                    'description' => 'processing...'
                ]);
            }

            // redirect to the products page
            return redirect(route('campaign.index'))->with('alert', [
                'type' => ($res->status ?? false) === true ? 'success' : 'danger',
                'msg' => ($res->status ?? false) === true ? 'Kampanya masih dalam proses, Anda bisa melihatnya di detail.' : ($res->msg ?? 'Terjadi Kesalahan Di Server')
            ]);
        }

        // redirect to the products page
        return redirect(route('campaign.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Kampanye Berhasil Dibuat!'
        ]);
    }
    
    /**
     * edit
     *
     * @param  mixed $campaign
     * @return void
     */
    public function edit(Campaign $campaign)
    {
        return view('pages.campaign.updateOrCreate', [
            'campaign' => $campaign->loadMissing('template'),
            'senders' => Number::whereBelongsTo(auth()->user())->get(),
            'cost' => Cost::all(),
        ]);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $campaign
     * @return void
     */
    public function update(CampaignRequest $request, Campaign $campaign)
    {

        $data = $request->validated();

        $result = static::processingFormat($request);

        $campaign->update([
            'user_id' => auth()->id(),
            'number_id' => $data['sender'],
            'title' => $data['title'],
            'slug' => $data['title'],
            'receivers' => $result['receivers'],
            'point' => $result['point'] ?? 0,
            'schedule' => $data['schedule'] ?? null,
            'is_manual' => $request->has('schedule') ? 0 : ($data['scheduling'] == '0' ? 1 : 0),
        ]);

        $campaign->template()->delete();
        $campaign->template()->create($result['result']);

        if ($data['scheduling'] == 'now') {
            SaveHistoryMessageJob::dispatchSync($campaign);

            $process = static::send(['campaign' => $campaign->id]);
            $res = json_decode($process);

            if (isset($res->status) && $res->status) {
                $campaign->update([
                    'executed_at' => now(),
                    'is_processing' => 1,
                    'description' => 'processing...'
                ]);
            }

            // redirect to the products page
            return redirect(route('campaign.index'))->with('alert', [
                'type' => ($res->status ?? false) === true ? 'success' : 'danger',
                'msg' => ($res->status ?? false) === true ? 'Kampanya masih dalam proses, Anda bisa melihatnya di detail.' : ($res->msg ?? 'Terjadi Kesalahan Di Server')
            ]);
        }

        // redirect to the products page
        return redirect(route('campaign.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Kampanye Berhasil Diperbarui!'
        ]);
        
    }
    
    /**
     * destroy
     *
     * @param  mixed $campaign
     * @return void
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->history()->delete();
        $campaign->messages()->delete();
        $campaign->template()->delete();
        $campaign->delete();

        return redirect(route('campaign.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Kampanye Berhasil Dihapus!'
        ]);
    }
    
    /**
     * get history campaign messages
     *
     * @param  mixed $campaign
     * @return void
     */
    public function history(Campaign $campaign)
    {
        if (request()->ajax()) {
            return view('pages.campaign.ajax.history',[
                'histories' => $campaign->messages()->get()
            ])->render();
        }

        return [];
    }
    
    /**
     * send By Queue Job Laravel
     *
     * @param  mixed $campaign
     * @return void
     */
    public function sendByJob(Campaign $campaign)
    {
        $campaign->loadMissing(['template', 'number']);

        $available = (Carbon::parse($campaign->number->start_time)->lt(Carbon::now()) && Carbon::parse($campaign->number->end_time)->gt(Carbon::now()));

        if (! $available || !$campaign->number->is_active) {
            return redirect(route('campaign.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Kampanye Tidak Tersedia!'
            ]);
        }

        SendMessageService::send($campaign);

        return redirect(route('campaign.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Kampanye Masih Dalam Proses!'
        ]);
    }
    
    /**
     * ajax type for type campaign
     *
     * @return void
     */
    public function type()
    {
        if (request()->ajax()) {
            if (request()->has('update')) {
                $campaign = Campaign::with('template')->find(request('update'));

                if (request('campaign') == 'template') {
                    return view('pages.campaign.ajax.template',[
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('campaign') == 'media') {
                    return view('pages.campaign.ajax.media', [
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('campaign') == 'button') {
                    return view('pages.campaign.ajax.button', [
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('campaign') == 'schedule') {
                    return view('pages.campaign.ajax.scheduling', [
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('campaign') == 'contact') {
                    return view('pages.campaign.ajax.contact', [
                        'contacts' => Contact::whereBelongsTo(auth()->user())->get(),
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('campaign') == 'location') {
                    return view('pages.campaign.ajax.location', [
                        'campaign' => $campaign
                    ])->render();
                }

                if (request('type') == 'tag') {
                    return view('pages.campaign.ajax.byTag', [
                        'tags' => Tag::whereBelongsTo(auth()->user())->get(),
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('type') == 'contact') {
                    return view('pages.campaign.ajax.byNumber', [
                        'contacts' => Contact::whereBelongsTo(auth()->user())->get(),
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('type') == 'random') {
                    return view('pages.campaign.ajax.byRandom', [
                        'contacts' => Contact::whereBelongsTo(auth()->user())->count(),
                        'campaign' => $campaign
                    ])->render();
                } elseif (request('type') == 'group') {
                    return view('pages.campaign.ajax.byGroup', [
                        'campaign' => $campaign
                    ])->render();
                }
            } else {
                if (request('campaign') == 'template') {
                    return view('pages.campaign.ajax.template')->render();
                } elseif (request('campaign') == 'media') {
                    return view('pages.campaign.ajax.media')->render();
                } elseif (request('campaign') == 'button') {
                    return view('pages.campaign.ajax.button')->render();
                } elseif (request('campaign') == 'schedule') {
                    return view('pages.campaign.ajax.scheduling')->render();
                } elseif (request('campaign') == 'contact') {
                    return view('pages.campaign.ajax.contact', [
                        'contacts' => Contact::whereBelongsTo(auth()->user())->get(),
                    ])->render();
                } elseif (request('campaign') == 'location') {
                    return view('pages.campaign.ajax.location')->render();
                }

                if (request('type') == 'tag') {
                    return view('pages.campaign.ajax.byTag', [
                        'tags' => Tag::whereBelongsTo(auth()->user())->get(),
                    ])->render();
                } elseif (request('type') == 'contact') {
                    return view('pages.campaign.ajax.byNumber', [
                        'contacts' => Contact::whereBelongsTo(auth()->user())->get(),
                    ])->render();
                } elseif (request('type') == 'random') {
                    return view('pages.campaign.ajax.byRandom', [
                        'contacts' => Contact::whereBelongsTo(auth()->user())->count()
                    ])->render();
                } elseif (request('type') == 'group') {
                    return view('pages.campaign.ajax.byGroup')->render();
                }
            }
        }
    }


    // BACK UP FLOW //

    /**
     * action
     *
     * @param  mixed $campaign
     * @return void
     */
    public function action(Campaign $campaign)
    {
        if (! is_null($campaign->executed_at)) {
            return redirect(route('campaign.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Kampanya Sudah Dieksekusi!'
            ]);
        }

        $campaign->loadMissing(['template', 'number', 'user.point']);

        if (Carbon::parse($campaign->user->point->expired_at)->lt(Carbon::now())) {
            return redirect(route('campaign.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Point Anda Telah Kadaluarsa!'
            ]);
        }

        $processing = Campaign::whereBelongsTo(auth()->user())->where('is_processing', 1)->exists();

        if ($processing) {
            return redirect(route('campaign.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Masih Ada Kampanya Yang Sedang Dalam Proses!'
            ]);
        }


        if ($campaign->number->status != 'Connected') {
            return redirect(route('campaign.index'))->with('alert', [
                'type' => 'danger',
                'msg' => 'Device Tidak Terkoneksi Dengan WhatsApp!'
            ]);
        }

        // Process
            SaveHistoryMessageJob::dispatchSync($campaign);

            $process = static::send(['campaign' => $campaign->id]);
            $res = json_decode($process);

            if (isset($res->status) && $res->status) {
                $campaign->update([
                    'executed_at' => now(),
                    'is_processing' => 1,
                    'description' => 'processing...'
                ]);
            }

            return redirect(route('campaign.index'))->with('alert', [
                'type' => ($res->status ?? false) === true ? 'success' : 'danger',
                'msg' => ($res->msg ?? 'Terjadi Kesalahan Di Server')
            ]);
        // Process
    }

    /**
     * send
     *
     * @param  mixed $campaign
     */
    private static function send($campaign)
    {
        try {
            return Http::asForm()->post(env('WA_URL_SERVER') . '/broadcast-delay2', $campaign);
        } catch (Exception $error) {
            Log::error($error);
            return 'false';
        }
    }
    
    /**
     * processingFormat
     *
     * @param  mixed $request
     * @return void
     */
    private static function processingFormat($request)
    {
        $data = $request->validated();

        $receivers = [
            'type' => $data['receiver'],
            'data' => [],
        ];

        if ($data['receiver'] == 'contact') {
            $receivers['data'] = Contact::whereBelongsTo(auth()->user())->whereIn('number', $data['numbers'])->get()->map(function ($contact) {
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
        } elseif ($data['receiver'] == 'tag') {
            // $receivers['data'] = Tag::find($data['tag'])->contacts->map(function ($contact) {
            //     return [
            //         'name' => $contact->name,
            //         'number' => $contact->number,
            //     ];
            // })->toArray();
            $receivers['name'] = Tag::find($data['tag'])->name;
            $receivers['id'] = $data['tag'];
        } elseif ($data['receiver'] == 'random') {
            $count = Contact::whereBelongsTo(auth()->user())->count();
            $receivers['id'] = ($data['random'] > $count ? $count : $data['random']);
        } elseif ($data['receiver'] == 'group') {
            $receivers['name'] = Group::find($data['group_list'])->title;
            $receivers['id'] = $data['group_list'];
            $receivers['is_broadcast'] = ($data['group_type'] == 'broadcast' ? true : false);
        } else {
            // $receivers['data'] = Contact::whereBelongsTo(auth()->user())->get()->map(function ($contact) {
            //     return [
            //         'name' => $contact->name,
            //         'number' => $contact->number,
            //     ];
            // })->toArray();
            $receivers['data'] = [];
        }


        // Point Cost
            $cost = Cost::all();
            $point = 0;
        // Point Cost

        if ($request->has('message')) {
            $result['text'] =  $data['message'];
            $length = floor(strlen($data['message']) / env('TEXT_MESSAGE_LENGTH', 100));
            $point = ($length < 1 ? 1 : $length) * ($cost->firstWhere('slug', 'text')->point ?? env('DEFAULT_POINT'));
            // $point = $cost->firstWhere('slug', 'text')->point;
        }

        if ($request->has('template-campaign')) {
            $template['text'] = $data['caption_template'];
            $template['footer'] = $data['footer_template'];
            $template['templateButtons'][] = [
                'index' => 1,
                'type' => $data['button-type'],
                'displayText' => $data['text'],
                'action' => $data['action'],
            ];

            if ($request->has('button-type2')) {
                $template['templateButtons'][] = [
                    'index' => 2,
                    'type' => $data['button-type2'],
                    'displayText' => $data['text2'],
                    'action' => $data['action2'],
                ];
            }

            $result['template'] = $template;

            // Point Cost
            $point += ($cost->firstWhere('slug', 'template')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        }

        if ($request->has('media-campaign')) {

            $media['caption'] = $data['caption_media'];
            $media['url'] = $data['media'];
            $result['media'] = $media;

            // Point Cost
            $point += ($cost->firstWhere('slug', 'media')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        }

        if ($request->has('button-campaign')) {
            $button['text'] = $data['caption_button'];
            $button['footer'] = $data['footer_button'];
            $button['buttons'][] = [
                'index' => 'id1',
                'type' => 1,
                'displayText' => $data['button1'],
            ];

            if ($request->filled('button2')) {
                $button['buttons'][] = [
                    'index' => 'id2',
                    'type' => 1,
                    'displayText' => $data['button2'],
                ];
            }

            if ($request->filled('button3')) {
                $button['buttons'][] = [
                    'index' => 'id3',
                    'type' => 1,
                    'displayText' => $data['button3'],
                ];
            }

            $result['button'] = $button;

            // Point Cost
            $point += ($cost->firstWhere('slug', 'button')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        }

        if ($request->has('contact-campaign')) {

            for ($i = 0; $i < count($data['vcard']); $i++) {
                $contact['vcard'][] = [
                    'number' => $data['vcard'][$i],
                ];
            }

            $result['contact'] = $contact;

            // Point Cost
            $point += ($cost->firstWhere('slug', 'contact')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        }

        if ($request->has('location-campaign')) {

            $location['lat'] = $data['latitude'];
            $location['long'] = $data['longitude'];

            $result['location'] = $location;

            // Point Cost
            $point += ($cost->firstWhere('slug', 'location')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        }

        if ($data['scheduling'] == 'now') {
            // Point Cost
            $point += ($cost->firstWhere('slug', 'now')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        } elseif ($data['scheduling'] == '0') {
            // Point Cost
            $point += ($cost->firstWhere('slug', 'manual')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        } else {
            // Point Cost
            $point += ($cost->firstWhere('slug', 'schedule')->point ?? env('DEFAULT_POINT'));
            // Point Cost
        }

        return [
            'result' => $result,
            'point' => $point,
            'receivers' => $receivers
        ];
    }
}
