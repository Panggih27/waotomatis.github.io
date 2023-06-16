<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim;
use PhpParser\Builder\Trait_;

class Product extends Model
{
    use HasFactory, SoftDeletes, GenerateUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title', 'slug', 'description', 'image',
        'price', 'point', 'duration', 'discount_type', 'discount', 'is_active',
        'created_by', 'updated_by'
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
     * setSlugAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }
    
    /**
     * createdBy
     *
     * @return void
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * updatedBy
     *
     * @return void
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * transaction
     *
     * @return void|Transaction
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
