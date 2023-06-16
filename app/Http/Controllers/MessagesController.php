<?php

namespace App\Http\Controllers;

use App\Models\Number;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MessagesController extends Controller
{

    public function index()
    {
        return view('pages.messagetest', [
            'numbers' => Number::whereBelongsTo(auth()->user())->get(),
        ]);
    }

    public function textMessageTest(Request $request)
    {
        $request->validate([
            'sender' => ['required', Rule::exists('numbers', 'id')->where('user_id', auth()->id())],
            'receiver' => ['required', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/'],
            'message' => ['required', 'string', 'max:255'],
        ]);

        $checking = Number::findOrFail($request->sender);

        if ($checking->status !== 'Connected' || !$checking->is_active) {
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => 'Your sender is not connected or not active!'
            ]);
        }

        $data = [
            'type' => 'text',
            'sender' => $checking->body,
            'receiver' => $request->receiver,
            'body' => ['text' => $request->message],
        ];

        return static::send($data);
    }

    public function mediaMessageTest(Request $request)
    {
        $request->validate([
            'sender' => ['required', Rule::exists('numbers', 'id')->where('user_id', auth()->id())],
            'receiver' => ['required', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/'],
            'caption' => ['required', 'string', 'max:255'],
            'media' => ['required_with:media-campaign', 'url'],
        ]);

        $checking = Number::findOrFail($request->sender);

        if ($checking->status !== 'Connected' || !$checking->is_active) {
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => 'Your sender is not connected or not active!'
            ]);
        }

        $data = [
            'type' => 'media',
            'sender' => $checking->body,
            'receiver' => $request->receiver,
            'body' => [
                'caption' => $request->caption,
                'url' => $request->media,
            ]
        ];

        return static::send($data);
    }

    public function buttonMessageTest(Request $request)
    {
        $request->validate([
            'sender' => ['required', Rule::exists('numbers', 'id')->where('user_id', auth()->id())],
            'receiver' => ['required', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/'],
            'caption' => ['required', 'string', 'max:255'],
            'footer' => ['required', 'string', 'max:50'],
            'button1' => ['required', 'string', 'max:50'],
            'button2' => ['nullable', 'string', 'max:50'],
            'button3' => ['nullable', 'string', 'max:50'],
        ]);

        $checking = Number::findOrFail($request->sender);

        if ($checking->status !== 'Connected' || !$checking->is_active) {
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => 'Your sender is not connected or not active!'
            ]);
        }

        
        $data = [
            'type' => 'button',
            'sender' => $checking->body,
            'receiver' => $request->receiver,
            'body' => [
                'caption' => $request->caption,
                'footer' => $request->footer,
                'data' => [ $request->button1, $request->button2, $request->button3 ]
            ]
        ];

        return static::send($data);
    }

    public function templateMessageTest(Request $request)
    {
        $request->validate([
            'sender' => ['required', Rule::exists('numbers', 'id')->where('user_id', auth()->id())],
            'receiver' => ['required', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/'],
            'caption' => ['required', 'string', 'max:255'],
            'footer' => ['required', 'string', 'max:50'],
            'button-type' => ['required', 'in:url,call'],
            'text' => ['required', 'string', 'max:15'],
            'action' => ['required', Rule::when(request('button-type') == 'url', ['url', 'string']), Rule::when(request('button-type') == 'call', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/')],
            'button-type2' => ['sometimes', 'required', 'in:url,call'],
            'text2' => ['sometimes', 'required', 'string', 'max:15'],
            'action2' => ['sometimes', 'required', Rule::when(request('button-type2') == 'url', ['url', 'string']), Rule::when(request('button-type2') == 'call', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/')],
        ]);

        $checking = Number::findOrFail($request->sender);

        if ($checking->status !== 'Connected' || !$checking->is_active) {
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => 'Your sender is not connected or not active!'
            ]);
        }
        $param = $request->all();

        $data = [
            'type' => 'template',
            'sender' => $checking->body,
            'receiver' => $request->receiver,
            'body' => [
                'caption' => $request->caption,
                'footer' => $request->footer,
                'data' => [
                    [
                        'type' => ($param['button-type'] == 'url' ? 'urlButton' : 'callButton'),
                        'text' => $request->text,
                        'action' => ($param['button-type'] == 'url' ? '' : '+') . $request->action
                    ],
                    [
                        'type' => (($param['button-type2'] ?? '') == 'url' ? 'urlButton' : 'callButton'),
                        'text' => $request->text2,
                        'action' => (($param['button-type2'] ?? '') == 'url' ? '' : '+') . $request->action2
                    ],
                ]
            ]
        ];

        return static::send($data);
    }
    
    /**
     * locationMessageTest
     *
     * @param  mixed $request
     * @return void
     */
    public function locationMessageTest(Request $request)
    {
        $request->validate([
            'sender' => ['required', Rule::exists('numbers', 'id')->where('user_id', auth()->id())],
            'receiver' => ['required', 'regex:/^[1-9]{3}?[0-9]{3}?[0-9]{4,8}$/'],
            'latitude' => ['required', 'string', 'regex:/^(-?\d+(\.\d+)?)$/'],
            'longitude' => ['required', 'string', 'regex:/^(-?\d+(\.\d+)?)$/'],
        ]);

        $checking = Number::findOrFail($request->sender);

        if ($checking->status !== 'Connected' || !$checking->is_active) {
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => 'Your sender is not connected or not active!'
            ]);
        }

        $data = [
            'type' => 'location',
            'sender' => $checking->body,
            'receiver' => $request->receiver,
            'body' => [
                'lat' => $request->latitude,
                'long' => $request->longitude,
            ]
        ];

        return static::send($data);
    }

    protected static function send(array $data)
    {
        try {
            $response = Http::withOptions(['verify' => false])->asForm()->post(env('WA_URL_SERVER') . '/send-message-test', $data);
            $res = json_decode($response);
            $alert = $res->status ? 'success' : 'danger';
            $msg = $res->msg;
        } catch (Exception $error) {
            Log::error($error);
            $alert = 'danger';
            $msg = 'There is error in your node server!';
        }

        return back()->with('alert', [
            'type' => $alert,
            'msg' => $msg
        ]);
    }
}
