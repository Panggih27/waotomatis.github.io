<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $data = Ticket::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('pages.ticket.index', [
            'data' => $data
        ]);
    }
}
