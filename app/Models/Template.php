<?php

namespace App\Models;

use App\Casts\ButtonCast;
use App\Casts\ContactCast;
use App\Casts\LocationCast;
use App\Casts\MediaCast;
use App\Casts\TemplateCast;
use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory, GenerateUuid;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'text', 'template', 'media', 'button', 'location', 'contact',
    ];
    
    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'template' => TemplateCast::class,
        'button' => ButtonCast::class,
        'media' => MediaCast::class,
        'contact' => ContactCast::class,
        'location' => LocationCast::class,
    ];
}
