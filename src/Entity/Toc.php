<?php

namespace Profounder\Entity;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;

class Toc extends Model
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
}
