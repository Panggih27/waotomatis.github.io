<?php

namespace App\Models;

use App\Casts\ReceiverCast;
use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory, GenerateUuid;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'number_id', 'title', 'slug', 'receivers', 'point', 'broadcast_point', 'schedule', 'is_manual', 'is_processing', 'description',
        'executed_at',
    ];

    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'receivers' => ReceiverCast::class
    ];
    
    /**
     * set Title Attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucwords(strtolower($value));
    }
    
    /**
     * set Slug Attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
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
     * template
     *
     */
    public function template()
    {
        return $this->hasOne(Template::class);
    }
    
    /**
     * messages
     *
     */
    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    /**
     * history
     *
     * @return History
     */
    public function history()
    {
        return $this->morphOne(History::class, 'historyable');
    }
}
