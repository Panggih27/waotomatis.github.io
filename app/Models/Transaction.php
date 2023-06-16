<?php

namespace App\Models;

use App\Casts\CouponCast;
use App\Casts\ProductCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * keyType of primary key
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * incrementing id false
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'bank_id', 'product_id', 'invoice', 'payment_code', 'fee', 'grand_total', 'product',
        'coupon', 'status', 'confirmation', 'cancelled_reason', 'edited_at', 'edited_by',
    ];
    
    /**
     * casts
     *
     * @var array
     */
    protected $casts = [
        'product' => ProductCast::class,
        'coupon' => CouponCast::class
    ];
    
    /**
     * booting generate id and invoice
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
            }

            $invoice = static::where(function ($q) {
                return $q->whereDate('created_at', date('Y-m-d'));
            })->count();
            
            $model->invoice = 'INV-' . date('Ymd') . sprintf('%03d', ($invoice + 1));
        });

        static::updating(function ($model) {
            $model->edited_at = now();
            $model->edited_by = auth()->id();
        });
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
     * product
     *
     * @return void
     */
    public function product_real()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    /**
     * bank
     *
     * @return void
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    
    /**
     * history
     *
     * @return void
     */
    public function history()
    {
        return $this->morphOne(History::class, 'historyable');
    }
}
