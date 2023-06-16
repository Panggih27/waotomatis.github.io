<?php

namespace App\Casts;

class ReceiverCast extends CastAbleObject
{
    public string $type;
    /** @var string|int|null */
    public $id;
    public ?string $name;
    public ?bool $is_broadcast;
    /** @var \App\Casts\DataReceiverCast[]|null */
    public $data;
}

class DataReceiverCast extends CastAbleObject
{
    public ?string $name;
    public string $number;
    public ?string $var1;
    public ?string $var2;
    public ?string $var3;
    public ?string $var4;
    public ?string $var5;
}
