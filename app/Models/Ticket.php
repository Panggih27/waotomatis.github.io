<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, GenerateUuid;
    
    protected $fillable = ['code', 'user_id', 'subject', 'message', 'red_color', 'read_for_user'];
}
