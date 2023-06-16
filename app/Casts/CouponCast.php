<?php

namespace App\Casts;

class CouponCast extends CastAbleObject
{
    public ?string $code;
    public ?float $discount;
    public ?string $discount_type;
    public ?string $description;

}
