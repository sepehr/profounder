<?php

namespace Profounder\Entity;

class Article extends Entity
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

    /**
     * Finds an article by its content ID.
     *
     * @param  string $contentId
     *
     * @return Article
     */
    public function findByContentId($contentId)
    {
        return $this->whereContentId($contentId)->first();
    }

    /**
     * Checks whether the article with the passed content ID exists or not.
     *
     * @param  string $contentId
     *
     * @return bool
     */
    public function existsByContentId($contentId)
    {
        return $this->whereContentId($contentId)->exists();
    }

    /**
     * Deletes associated Toc records.
     *
     * @return bool
     */
    public function deleteToc()
    {
        return $this->toc()->delete();
    }
}
