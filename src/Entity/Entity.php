<?php

namespace Profounder\Entity;

use Illuminate\Database\Eloquent\Model;

abstract class Entity extends Model
{
    /**
     * Fills an entity with data from an array and updates it.
     *
     * @param  array $attributes
     *
     * @return bool
     */
    public function fillAndSave(array $attributes)
    {
        return $this->fill($attributes)->save();
    }

    /**
     * Creates an entity only if not exists.
     *
     * @param  array $attributes
     *
     * @return static
     */
    public function existsOrCreate(array $attributes)
    {
        return $this->firstOrCreate($attributes);
    }
}
