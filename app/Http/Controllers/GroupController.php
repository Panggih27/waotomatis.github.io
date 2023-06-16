<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GroupController extends Controller
{    
    /**
     * show
     *
     * @param  mixed $number
     * @return void
     */
    public function show(Number $number)
    {
        $group = Group::where('number_id', $number->id)->get();

        return $group;
    }
    
    /**
     * fetch
     *
     * @param  mixed $sender
     * @return void
     */
    public function fetch($sender)
    {
        $sender = Number::findOrFail($sender)->body;
        if (request()->ajax()) {
            return Http::asForm()->get(env('WA_URL_SERVER') . '/fetch-all-group/' . $sender)->json();
        }

        return [];
    }
    
    /**
     * participant
     *
     * @param  mixed $sender
     * @param  mixed $jid
     * @return void
     */
    public function participant($sender, $jid)
    {
        if (request()->ajax()) {
            return Http::asForm()->get(env('WA_URL_SERVER') . '/metadata-group/' . $sender . '/' . $jid)->json();
        }

        return [];
    }
}
