<?php

namespace App\Casts;

class ContactCast extends CastAbleObject
{
    /** @var \App\Casts\NumberCast[] */
    public $vcard;
}

class NumberCast extends CastAbleObject
{
    public string $number;
}