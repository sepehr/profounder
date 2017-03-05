<?php

namespace Profounder\Persistence\Entity\Eloquent;

class Publisher extends Entity
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

    /**
     * @inheritdoc
     */
    public function createArticle(array $article)
    {
        return $this->articles()->create($article);
    }
}
