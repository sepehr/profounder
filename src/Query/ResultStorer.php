<?php

namespace Profounder\Query;

use Profounder\Utils;
use Illuminate\Database\Capsule\Manager as Capsule;

class ResultStorer
{
    /**
     * Capsule manager instance.
     *
     * @var Capsule
     */
    private $capsule;

    /**
     * Utils instance.
     *
     * @var Utils
     */
    private $utils;

    /**
     * ResultStorer constructor.
     *
     * @param  Capsule $capsule
     * @param  Utils $utils
     */
    public function __construct(Capsule $capsule, Utils $utils)
    {
        $this->utils   = $utils;
        $this->capsule = $capsule;
    }

    /**
     * Static factory method.
     *
     * @param  array $args
     *
     * @return ResultStorer
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * Stores a collection of articles into the database.
     *
     * @param  array $articles
     *
     * @return int Number of successful inserts.
     */
    public function store(array $articles)
    {
        $count = 0;
        foreach ($articles as $article) {
            $this->insertIfNotExists($article) and $count++;
        }

        return $count;
    }

    /**
     * Inserts a new article record into the database if necessary.
     *
     * @param  array $article
     *
     * @return bool
     */
    private function insertIfNotExists(array $article)
    {
        $queryBuilder = $this->capsule->table('articles');

        if ($queryBuilder->where(['internal_id' => $article['InternalId']])->exists()) {
            return false;
        }

        return $queryBuilder->insert($this->prepareArticle($article));
    }

    /**
     * Prepares an article array for insertion.
     *
     * @param  array $article
     *
     * @return array
     */
    private function prepareArticle(array $article)
    {
        return [
            'sku'         => $article['Sku'],
            'content_id'  => $article['ContentId'],
            'publisher'   => $article['Publisher'],
            'internal_id' => $article['InternalId'],
            'title'       => $this->utils->stripTags($article['Title']),
            'price'       => $this->utils->preparePrice($article['Price']),
            'date'        => $this->utils->reformatDate($article['DocDateTime']),
        ];
    }
}
