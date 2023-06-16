<?php

namespace App\Casts;

class ButtonCast extends CastAbleObject
{
    public ?string $text;
    public string $footer;
    /** @var \App\Casts\DataButtonCast[] */
    public $buttons;
}

class DataButtonCast extends CastAbleObject
{
    public string $index;
    public string $displayText;
    public int $type;
}