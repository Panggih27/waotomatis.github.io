<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'campaign_id', 'receiver', 'sender', 'body', 'type', 'executed_at', 'point', 'status', 'status_description'
    ];
    
    /**
     * messageable
     *
     * @return void
     */
    public function messageable()
    {
        return $this->morphTo();
    }
}
