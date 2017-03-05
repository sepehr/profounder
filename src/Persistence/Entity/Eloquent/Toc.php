<?php

namespace Profounder\Persistence\Entity\Eloquent;

use Kalnoy\Nestedset\NodeTrait;

class Toc extends Entity
{
    use NodeTrait;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = ['article_id', 'title', 'price'];

    /**
     * Default timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * A TOC belongs to an article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Returns scoped fields of the nested set.
     *
     * @return array
     */
    protected function getScopeAttributes()
    {
        return ['article_id'];
    }
}
