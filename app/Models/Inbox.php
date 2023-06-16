<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inbox extends Model
{
    use HasFactory, GenerateUuid;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ['number', 'message_id', 'sender', 'body'];
    
    /**
     * contact
     *
     * @return void|Contact
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'sender', 'number');
    }
}
