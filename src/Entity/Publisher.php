<?php

namespace Profounder\Entity;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Default timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * A publisher has many articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
