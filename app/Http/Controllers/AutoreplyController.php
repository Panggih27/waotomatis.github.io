<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutoReplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Autoreply;
use App\Models\Number;
use Illuminate\Support\Str;

class AutoreplyController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.autoreply.index', [
            'autoreplies' => Autoreply::whereBelongsTo(auth()->user())->with(['number'])->get()
        ]);
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('pages.autoreply.create', [
            'numbers' => Number::whereBelongsTo(auth()->user())->where('status', 'Connected')->get(),
        ]);
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(AutoReplyRequest $request)
    {
        $data = $request->validated();

        switch ($data['type']) {
            case 'template':
                $result['caption'] = $data['caption'];
                $result['footer'] = $data['footer'];

                $typeButton1 = $data['button-type'];
                $result['templateButtons'][] = [
                    'index' => 1,
                    $typeButton1 . 'Button' => [
                        'displayText' => $data['text'],
                        ($typeButton1 == 'call' ? 'phoneNumber' : $typeButton1) => ($typeButton1 == 'call' ? '+' : '') . $data['action']
                    ]
                ];

                if ($request->has('button-type2')) {
                    $typeButton2 = $data['button-type2'];
                    $result['templateButtons'][] = [
                        'index' => 2,
                        $typeButton2 . 'Button' => [
                            'displayText' => $data['text2'],
                            ($typeButton2 == 'call' ? 'phoneNumber' : $typeButton2) => ($typeButton2 == 'call' ? '+' : '') . $data['action2']
                        ]
                    ];
                }
                break;
            case 'media':
                $result['url'] = $data['media'];
                $result['caption'] = $data['caption'];
                break;
            case 'button':
                $result['caption'] = $data['caption'];
                $result['footer'] = $data['footer'];
                $result['data'] = [$data['button1'], $data['button2'] ?? null, $data['button3'] ?? null];
                break;
            case 'location':
                $result['lat'] = $data['latitude'];
                $result['long'] = $data['longitude'];
                break;

            default:
                $result['text'] = $data['caption'];
                break;
        }

        Autoreply::create([
            'user_id' => auth()->id(),
            'number_id' => $data['sender'],
            'keyword' => $data['keyword'],
            'search_type' => $data['search'],
            'reply' => json_encode($result),
            'reply_type' => $data['type'],
        ]);

        return redirect(route('autoreply.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Your auto reply was added!'
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function show(Autoreply $autoreply, Request $request)
    {
        return view('pages.autoreply.detail', [
            'autoreply' => $autoreply->id,
            'recipient' => $autoreply->messages()->count(),
            'pending' => $autoreply->messages()->where('status', 'pending')->count(),
            'success' => $autoreply->messages()->where('status', 'success')->count(),
            'failed' => $autoreply->messages()->where('status', 'failed')->count(),
        ]);
    }
    
    /**
     * edit
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function edit($id, Request $request)
    {
        return view('pages.autoreply.create', [
            'numbers' => Number::whereBelongsTo(auth()->user())->where('status', 'Connected')->get(),
            'autoreply' => Autoreply::findOrFail($id),
        ]);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $autoreply
     * @return void
     */
    public function update(AutoReplyRequest $request, Autoreply $autoreply)
    {
        $data = $request->validated();

        switch ($data['type']) {
            case 'template':

                $result['caption'] = $data['caption'];
                $result['footer'] = $data['footer'];
                $result['data'][] = [
                    'type' => ($data['button-type'] == 'url' ? 'urlButton' : 'callButton'),
                    'text' => $data['text'],
                    'action' => ($data['button-type'] == 'url' ? '' : '+') . $data['action']
                ];

                if ($request->has('button-type2')) {
                    $result['data'][] = [
                        'type' => ($data['button-type2'] == 'url' ? 'urlButton' : 'callButton'),
                        'text' => $data['text2'],
                        'action' => ($data['button-type2'] == 'url' ? '' : '+') . $data['action2']
                    ];
                }
                break;
            case 'media':
                $result['url'] = $data['media'];
                $result['caption'] = $data['caption'];
                break;
            case 'button':
                $result['caption'] = $data['caption'];
                $result['footer'] = $data['footer'];
                $result['data'] = [$data['button1'], $data['button2'] ?? null, $data['button3'] ?? null];
                break;
            case 'location':
                $result['lat'] = $data['latitude'];
                $result['long'] = $data['longitude'];
                break;

            default:
                $result['text'] = $data['caption'];
                break;
        }

        $autoreply->update([
            'number_id' => $data['sender'],
            'keyword' => $data['keyword'],
            'search_type' => $data['search'],
            'reply' => json_encode($result),
            'reply_type' => $data['type'],
        ]);

        return redirect(route('autoreply.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Your auto reply was updated!'
        ]);
    }
    
    /**
     * destroy
     *
     * @param  mixed $autoreply
     * @return void
     */
    public function destroy(Autoreply $autoreply)
    {
        $autoreply->delete();
        return redirect(route('autoreply.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Deleted'
        ]);
    }
    
    /**
     * destroyAll
     *
     * @param  mixed $request
     * @return void
     */
    public function destroyAll(Request $request)
    {
        Autoreply::whereUserId(Auth::user()->id)->delete();
        return redirect(route('autoreply.index'))->with('alert', [
            'type' => 'success',
            'msg' => 'Deleted'
        ]);
    }
    
    /**
     * getFormByType
     *
     * @param  mixed $type
     * @param  mixed $request
     * @return void
     */
    public function getFormByType($type, Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('edit') && $request->filled('edit') && Str::isUuid($request->edit)) {
                $autoreply = Autoreply::find($request->edit);
            }
            switch ($type) {
                case 'text': 
                    return view('pages.autoreply.ajax.formtext', [
                        'autoreply' => $autoreply ?? null
                    ])->render();
                    break;
                case 'media':
                    return view('pages.autoreply.ajax.formimage',[
                        'autoreply' => $autoreply ?? null
                    ])->render();
                    break;
                case 'button':
                    return view('pages.autoreply.ajax.formbutton', [
                        'autoreply' => $autoreply ?? null
                    ])->render();
                    break;
                case 'template':
                    return view('pages.autoreply.ajax.formtemplate',[
                        'autoreply' => $autoreply ?? null
                    ])->render();
                    break;
                case 'location':
                    return view('pages.autoreply.ajax.formlocation',[
                        'autoreply' => $autoreply ?? null
                    ])->render();
                    break;
                default:
                    return view('pages.autoreply.ajax.formtext', [
                        'autoreply' => $autoreply ?? null
                    ])->render();
                    break;
            }
        }
        return 'http request';
    }
    
    /**
     * showRespond
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function showRespond($id, Request $request)
    {
        if ($request->ajax()) {
            $dataAutoReply = Autoreply::find($id);
            $decode = json_decode($dataAutoReply->reply);

            switch ($dataAutoReply->reply_type) {
                case 'text':
                    return view('pages.autoreply.ajax.textshow', [
                        'keyword' => $dataAutoReply->keyword,
                        'text' => $decode->text
                    ])->render();
                    break;
                case 'media':
                    return  view('pages.autoreply.ajax.imageshow', [
                        'keyword' => $dataAutoReply->keyword,
                        'caption' => $decode->caption,
                        'image' => $decode->url,
                    ])->render();
                    break;
                case 'button':
                    return  view('pages.autoreply.ajax.buttonshow', [
                        'keyword' => $dataAutoReply->keyword,
                        'message' => $decode->caption,
                        'footer' => $decode->footer,
                        'buttons' => $decode->data,
                    ])->render();
                    break;
                case 'template':
                    return  view('pages.autoreply.ajax.templateshow', [
                        'keyword' => $dataAutoReply->keyword,
                        'message' => $decode->caption,
                        'footer' => $decode->footer,
                        'template1' => $decode->data[0],
                        'template2' => count($decode->data) > 1 ? $decode->data[1] : null

                    ])->render();
                    break;
                case 'location':
                    return  view('pages.autoreply.ajax.locationshow', [
                        'keyword' => $dataAutoReply->keyword,
                        'bbox' => getBbox($decode->lat, $decode->long),
                    ])->render();
                    break;
                default:
                    # code...
                    break;
            }
        }
    }
    
    /**
     * detail
     *
     * @param  mixed $autoreply
     * @return void
     */
    public function detail(Autoreply $autoreply)
    {
        return view('pages.autoreply.detail', [
            'autoreply' => $autoreply->id,
            'recipient' => $autoreply->messages()->count(),
            'pending' => $autoreply->messages()->where('status', 'pending')->count(),
            'success' => $autoreply->messages()->where('status', 'success')->count(),
            'failed' => $autoreply->messages()->where('status', 'failed')->count(),
        ]);
    }
    
    /**
     * history
     *
     * @param  mixed $autoreply
     * @return void
     */
    public function history(Autoreply $autoreply)
    {
        if (request()->ajax()) {
            return view('pages.autoreply.ajax.history', [
                'histories' => $autoreply->messages()->get()
            ])->render();
        }

        return [];
    }
}
