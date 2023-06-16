<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    
    /**
     * dashboard
     *
     * @return void
     */
    public function dashboard()
    {
        return view('home', [
            'numbers' => Auth::user()->numbers()->get()
        ]);
    }
}
