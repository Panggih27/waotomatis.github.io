<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'point','type',
    ];
    
    /**
     * historyable
     *
     * @return void
     */
    public function historyable()
    {
        return $this->morphTo();
    }
    
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
