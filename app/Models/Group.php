<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory, GenerateUuid;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'number_id', 'jid', 'title', 'participant_count', 'is_mine'
    ];
    
    /**
     * number
     *
     * @return void
     */
    public function number()
    {
        return $this->belongsTo(Number::class);
    }
}
