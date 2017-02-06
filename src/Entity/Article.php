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
        'content_id', 'publisher_id', 'title', 'sku', 'price', 'date', 'internal_id', 'length', 'abstract', 'toctext'
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

    /**
     * An article belongs to a publisher.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
