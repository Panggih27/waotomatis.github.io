<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ['user_id', 'name'];
    
    /**
     * contacts
     *
     * @return void
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
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
