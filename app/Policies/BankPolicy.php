<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankPolicy
{
    use HandlesAuthorization;

    public function save(User $user, Bank $bank)
    {
        return $user->id === $bank->user_id;
    }
}
