<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as AuthCanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, AuthCanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, CanResetPassword, GenerateUuid, HasRoles;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAvatarAttribute()
    {
        return 'https://ui-avatars.com/api/?name=' . str_replace(' ', '+', $this->name) . '&background=4e73df&color=ffffff&size=100';
    }

    /**
     * numbers
     *
     * @return void|Number
     */
    public function numbers()
    {
        return $this->hasMany(Number::class);
    }

    /**
     * autoreplies
     *
     * @return void|Autoreply
     */
    public function autoreplies()
    {
        return $this->hasMany(Autoreply::class);
    }

    /**
     * contacts
     *
     * @return void|Contact
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * tags
     *
     * @return void|Tag
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * blasts
     *
     * @return void
     */
    public function blasts()
    {
        return $this->hasMany(Blast::class);
    }
    
    /**
     * point
     *
     * @return void|Point
     */
    public function point()
    {
        return $this->hasOne(Point::class);
    }
    
    /**
     * histories
     *
     * @return void
     */
    public function histories()
    {
        return $this->hasMany(History::class);
    }
    
    /**
     * transactions
     *
     * @return void
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    /**
     * messages
     *
     * @return void
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
