<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.change-pass');
    }
    
    /**
     * changing the password
     *
     * @param  mixed $request
     * @return void
     */
    public function store(ChangePasswordRequest $request)
    {
        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('alert', [
            'type' => 'success',
            'msg' => 'Password Changed Success!'
        ]);
    }
}
