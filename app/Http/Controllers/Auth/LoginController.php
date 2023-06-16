<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    
    /**
     * show view login
     *
     * @return void
     */
    public function index()
    {
        return view('auth.login');
    }
    
    /**
     * authentication
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only(['email', 'password']))) {
            return redirect('/home');
        }

        return back()->with('alert', [
            'type' => 'danger',
            'msg' => 'Invalid Credential!'
        ]);
    }
}
