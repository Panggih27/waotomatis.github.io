<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GenerateUuid
{
        
    /**
     * boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->keyType = 'string';
            $model->incrementing = false;

            $model->{$model->getKeyName()} = $model->{$model->getKeyName()} ?: (string) Str::orderedUuid();
        });
    }
    
    /**
     * getIncrementing
     *
     * @return void
     */
    public function getIncrementing()
    {
        return false;
    }
    
    /**
     * getKeyType
     *
     * @return void
     */
    public function getKeyType()
    {
        return 'string';
    }
}