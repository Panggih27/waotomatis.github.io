<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return view('pages.users.index', [
            'users' => User::whereHas('roles', function($q){
                $q->where('name', 'customer');
            })->with(['point', 'numbers'])->get(),
        ]);
    }
    
    /**
     * transaction
     *
     * @param  mixed $user
     * @return void
     */
    public function transaction(User $user)
    {
        // return $user->loadMissing('transactions');
        return view('pages.users.transaction', [
            'user' => $user->loadMissing('transactions'),
        ]);
    }

    public function point(User $user)
    {
        // return $user->loadMissing('histories');
        return view('pages.users.point', [
            'user' => $user->loadMissing(['histories.historyable']),
        ]);
    }
}
