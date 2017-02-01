<?php

namespace Profounder\Entity;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'internal_id', 'content_id', 'title', 'sku', 'publisher', 'price', 'date', 'length', 'abstract', 'toctext'
    ];

    /**
     * Timestamps.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Default timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * An article has one TOC.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function toc()
    {
        return $this->hasOne(Toc::class);
    }
}
