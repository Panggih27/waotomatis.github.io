<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Point;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return response()->json(view('pages.point.history', ['histories' => History::whereBelongsTo(auth()->user())->with('historyable')->latest()->get()])->render());
        }

        return view('pages.point.index',[
            'point' => Point::whereBelongsTo(auth()->user())->first()
        ]);
    }
}
