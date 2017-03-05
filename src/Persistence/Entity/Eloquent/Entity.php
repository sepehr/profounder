<?php

namespace Profounder\Persistence\Entity\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class Entity extends Model implements EntityContract
{
    /**
     * @inheritdoc
     */
    public function fillAndSave(array $attributes)
    {
        return $this->fill($attributes)->save();
    }

    /**
     * @inheritdoc
     */
    public function existsOrCreate(array $attributes)
    {
        return $this->firstOrCreate($attributes);
    }
}
