<?php

namespace Profounder\Persistence\Entity;

use Illuminate\Contracts\Support\Arrayable;

interface EntityContract extends Arrayable
{
    /**
     * Persists entity data.
     *
     * @param array $attributes
     *
     * @return EntityContract
     */
    public static function create(array $attributes = []);

    /**
     * Fills an entity with data from an array and updates it.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function fillAndSave(array $attributes);

    /**
     * Creates an entity only if not exists.
     *
     * @param array $attributes
     *
     * @return static
     */
    public function existsOrCreate(array $attributes);
}
