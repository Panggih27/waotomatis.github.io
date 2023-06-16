<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'tag_id', 'name', 'number', 'var1', 'var2', 'var3', 'var4', 'var5'
    ];

    /**
     * tag
     *
     * @return void|Tag
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
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
