<?php

namespace App\Casts;

class ProductCast extends CastAbleObject
{
    public string $title;
    public string $slug;
    public string $description;
    public string $image;
    public int $price;
    public int $point;
    public int $duration;
    public ?string $discount_type;
    public ?int $discount;
}
