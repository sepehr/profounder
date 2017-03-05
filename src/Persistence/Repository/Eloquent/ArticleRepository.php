<?php

namespace Profounder\Persistence\Repository\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Profounder\Persistence\Entity\ArticleContract;
use Profounder\Persistence\Entity\Eloquent\Article;
use Profounder\Persistence\Entity\Eloquent\EntityContract;
use Profounder\Persistence\Repository\ArticleRepositoryContract;

class ArticleRepository extends Repository implements ArticleRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function entity()
    {
        return Article::class;
    }

    /**
     * @inheritdoc
     */
    public function findByContentId($contentId)
    {
        return $this->entity->whereContentId($contentId)->first();
    }

    /**
     * @inheritdoc
     */
    public function existsByContentId($contentId)
    {
        return $this->entity->whereContentId($contentId)->exists();
    }

    /**
     * @inheritdoc
     */
    public function executeOnNonAugmentedWithin($minId, $maxId, $chunk, $callback)
    {
        $this
            ->withNonAugmented()
            ->select('id', 'content_id')
            ->whereBetween('id', [$minId, $maxId])
            ->chunk($chunk, $callback);
    }

    /**
     * @inheritdoc
     */
    public function getNonAugmentedIdByOffset($offset)
    {
        return $this
            ->withNonAugmented()
            ->skip($offset)
            ->take(1)
            ->first()
            ->id;
    }

    /**
     * @inheritdoc
     */
    public function dumpSku()
    {
        $output = '';

        $this
            ->entity
            ->select('sku')
            ->chunk(1000, function (Collection $skus) use (&$output) {
                $output .= $skus->reduce(function ($carry, ArticleContract $item) {
                    return $carry .= $item->getSku() . PHP_EOL;
                }, '');
            });

        return $output;
    }

    /**
     * @inheritdoc
     */
    public function dumpCsv()
    {
        $output = 'ContentID,Title,Date,Price,SKU,Length' . PHP_EOL;

        $this
            ->entity
            ->select('content_id', 'title', 'date', 'price', 'sku', 'length')
            ->chunk(1000, function (Collection $articles) use (&$output) {
                $output .= $articles->reduce(function ($carry, ArticleContract $item) {
                    return $carry .= '"' . implode('","', $item->toArray()) . '"' . PHP_EOL;
                }, '');
            });

        return $output;
    }

    /**
     * Sets the query conditions for non-augmented articles.
     *
     * @return EntityContract
     */
    private function withNonAugmented()
    {
        $this->entity->whereNull('length')->orderBy('id');

        return $this->entity;
    }
}
