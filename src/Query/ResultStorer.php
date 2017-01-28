<?php

namespace Profounder\Query;

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
     * ResultStorer constructor.
     *
     * @param  Capsule $capsule
     */
    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }

    /**
     * Static factory method.
     *
     * @param  Capsule $capsule
     *
     * @return ResultStorer
     */
    public static function create(Capsule $capsule)
    {
        return new static($capsule);
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
            'title'       => $this->prepareTitle($article['Title']),
            'price'       => $this->preparePrice($article['Price']),
            'date'        => $this->prepareDate($article['DocDateTime']),
        ];
    }

    /**
     * Reformats a valid date string for insertion.
     *
     * @param  string $date
     *
     * @return string
     */
    private function prepareDate($date)
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }

    /**
     * Converts a price string to equivalent integer.
     *
     * @param  string $price
     *
     * @return int
     */
    private function preparePrice($price)
    {
        return intval(preg_replace('/([^0-9\\.])/i', '', $price) * 100);
    }

    /**
     * Prepares article title for insertion.
     *
     * @param  string $title
     *
     * @return string
     */
    private function prepareTitle($title)
    {
        return strip_tags($title);
    }
}
