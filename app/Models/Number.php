<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'body', 'webhook', 'status', 'start_time', 'end_time', 'delay', 'is_active'
    ];

    /**
     * user
     *
     * @return void|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
        
    /**
     * groups
     *
     * @return void|Group
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    
    /**
     * autoreply
     *
     * @return void|Autoreply
     */
    public function autoreplies()
    {
        return $this->hasMany(Autoreply::class);
    }
    
    /**
     * campaigns
     *
     * @return void|Campaign
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
