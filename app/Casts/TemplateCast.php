<?php

namespace App\Casts;

// column template
class TemplateCast extends CastAbleObject
{
    public ?string $text;
    public string $footer;
    /** @var \App\Casts\DataTemplateCast[] */
    public $templateButtons;
}

class DataTemplateCast extends CastAbleObject
{
    public int $index;
    public string $type;
    public string $displayText;
    public string $action;
}
