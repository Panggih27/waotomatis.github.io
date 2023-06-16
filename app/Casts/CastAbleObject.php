<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Spatie\DataTransferObject\DataTransferObject;

abstract class CastAbleObject extends DataTransferObject implements Castable
{
    public static function castUsing(array $arguments)
    {
        return new DTOCast(static::class);
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public static function fromJson($json)
    {
        return new static(json_decode($json, true));
    }
}

class DTOCast implements CastsAttributes
{
    protected string $class;

    /**
     * @param string $class The DataTransferObject class to cast to
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Cast the stored value to the configured DataTransferObject.
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return;
        }

        return new $this->class(json_decode($value, true));
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return;
        }

        if (is_array($value)) {
            $value = new $this->class($value);
        }

        if (!$value instanceof $this->class) {
            throw new InvalidArgumentException("Value must be of type [$this->class], array, or null");
        }

        return json_encode($value);
    }
}

