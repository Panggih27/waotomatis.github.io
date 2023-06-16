<?php

namespace App\Models;

use App\Casts\BankCast;
use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'bank', 'account_name', 'account_number', 'status', 'is_owner',
    ];
    
    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'bank' => BankCast::class
    ];
    
    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
