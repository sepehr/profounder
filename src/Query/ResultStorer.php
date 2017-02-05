<?php

namespace Profounder\Query;

use Profounder\Utils;
use Profounder\Entity\Article;

class ResultStorer
{
    /**
     * Article repository instance.
     *
     * @var Article
     */
    private $repository;

    /**
     * Utils instance.
     *
     * @var Utils
     */
    private $utils;

    /**
     * ResultStorer constructor.
     *
     * @param  Article $repository
     * @param  Utils $utils
     */
    public function __construct(Article $repository, Utils $utils)
    {
        $this->repository = $repository;
        $this->utils      = $utils;
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
        if ($this->repository->whereInternalId($article['InternalId'])->exists()) {
            return false;
        }

        return (bool) $this->repository->create($this->prepareArticle($article));
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
