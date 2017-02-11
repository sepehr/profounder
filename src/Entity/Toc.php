<?php

namespace Profounder\Entity;

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
     * Returns scoped fields of the nested set.
     *
     * @return array
     */
    protected function getScopeAttributes()
    {
        return ['article_id'];
    }

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
     * Creates a Toc associated with the passed article ID.
     *
     * @param  array $toc
     * @param  int $articleId
     *
     * @return bool
     */
    public function createArticleToc(array $toc, $articleId)
    {
        return (bool) $this->create([
            'children'   => $toc,
            'article_id' => $articleId,
        ]);
    }
}
