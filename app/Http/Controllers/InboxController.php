<?php

namespace App\Http\Controllers;

use App\Exports\InboxExport;
use App\Models\Inbox;
use App\Models\Number;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InboxController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.inbox.index', [
            'device' => Number::whereBelongsTo(auth()->user())->get()
        ]);
    }
    
    /**
     * show
     *
     * @param  mixed $number
     * @return void
     */
    public function show($number)
    {
        $inboxes = Inbox::with('contact')->where('number', 'like', $number . '%')->
        when(request()->filled('min'), function($q) {
            $q->where('created_at', '>=', Carbon::parse(request('min')));
        })->when(request()->filled('max'), function($q) {
            $q->where('created_at', '<=', Carbon::parse(request('max')));
        })->get();
        
        if (request()->ajax()) {
            return  view('pages.inbox.data', [
                'inboxes' => $inboxes,
            ])->render();
        }

        if (request()->has('download')) {
            return (new InboxExport($inboxes))->download($number . '-inboxes.xlsx');

            return redirect()->back()->with('alert', [
                'type' => 'success',
                'msg' => 'Pesan Proses Download',
            ]);
        }

        return [];
    }
    
    /**
     * destroy
     *
     * @param  mixed $number
     * @param  mixed $id
     * @return void
     */
    public function destroy($number, $id)
    {
        $inboxes = Inbox::where('number', 'like', $number . '%')->where('id', $id)->firstOrFail();
        $inboxes->delete();

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'msg' => 'Pesan Berhasil Dihapus',
        ]);
    }

    public function search(Request $request, $number)
    {
        if ($request->ajax()) {
            $inboxes = Inbox::with('contact')->where('number', 'like', $number . '%')->
            when($request->has('min'), function($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->min));
            })->when($request->has('max'), function($q) use ($request) {
                $q->where('created_at', '<=', Carbon::parse($request->max));
            })->get();
            
            return  view('pages.inbox.data', [
                'inboxes' => $inboxes,
            ])->render();
        }
    }
}
