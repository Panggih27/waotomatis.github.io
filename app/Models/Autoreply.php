<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Autoreply extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ['user_id', 'number_id', 'keyword', 'search_type', 'reply', 'reply_type'];
    
    /**
     * setKeywordAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setKeywordAttribute($value)
    {
        $this->attributes['keyword'] = strtolower($value);
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
    
    /**
     * number
     *
     * @return void
     */
    public function number()
    {
        return $this->belongsTo(Number::class);
    }

    /**
     * message
     *
     * @return Message
     */
    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }
    
    /**
     * histories
     *
     * @return void|History
     */
    public function histories()
    {
        return $this->morphMany(History::class, 'historyable');
    }
}
