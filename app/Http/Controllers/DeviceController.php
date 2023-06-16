<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberRequest;
use App\Models\Inbox;
use App\Models\Number;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeviceController extends Controller
{    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(NumberRequest $request)
    {
        $data = $request->validated();

        $create = [
            'user_id' => Auth::user()->id,
            'name' => $data['name'],
            'body' => $data['sender'],
            'start_time' => $data['start'],
            'end_time' => $data['end'],
            // 'webhook' => $data['urlwebhook'],
            'status' => 'Disconnect',
            'is_active' => $data['active']
        ];

        if ($data['delay_type'] == 'time') {
            $create['delay'] = $data['delay'];
        } else {
            $create['delay'] = $data['delay_from'] . ' - ' . $data['delay_to'];
        }

        Number::create($create);

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Device Berhasil Ditambahkan!'
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $device
     * @return void
     */
    public function show(Number $device)
    {

        if (request()->ajax()) {
            return json_encode($device);
        }

        return $device;
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $device
     * @return void
     */
    public function update(NumberRequest $request, Number $device)
    {
        $data = $request->validated();

        $create = [
            'name' => $data['name'],
            'body' => $data['sender'],
            'start_time' => $data['start'],
            'end_time' => $data['end'],
            // 'webhook' => $data['urlwebhook'],
            // 'status' => 'Disconnect',
            'is_active' => $data['active']
        ];

        if ($data['delay_type'] == 'time') {
            $create['delay'] = $data['delay'];
        } else {
            $create['delay'] = $data['delay_from'] . ' - ' . $data['delay_to'];
        }

        $device->update($create);

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Device Berhasil Diperbarui!'
        ]);
    }

    /**
     * destroy
     *
     * @param  mixed $request
     * @return void
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $device = Number::with(['autoreplies', 'campaigns'])->find($id);

            $device->groups()->delete();

            $device->autoreplies->each(function ($autoreply) {
                $autoreply->histories()->delete();
                $autoreply->messages()->delete();
            });
            $device->autoreplies()->delete();

            $device->campaigns->each(function ($campaign) {
                $campaign->history()->delete();
                $campaign->messages()->delete();
                $campaign->template()->delete();
            });
            $device->campaigns()->delete();

            Inbox::where('number', 'like', $device->body. ':%')->delete();

            $device->delete();

            File::deleteDirectory(base_path('sessions/' . $device->body));
            DB::commit();
        } catch (Exception $error) {
            DB::rollBack();
            return back()->with('alert', [
                'type' => 'danger',
                'msg' => 'Terjadi Kesalahan Di Server!'
            ]);
        }

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Device Berhasil Dihapus!'
        ]);
    }
    
    /**
     * activating
     *
     * @param  mixed $id
     * @return void
     */
    public function activating($id)
    {
        $device = Number::findOrFail($id);

        $device->update([
            'is_active' => !$device->is_active
        ]);

        return true;
    }
}
