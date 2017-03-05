<?php
namespace Profounder\Query\Storer;

use Illuminate\Support\Collection;

interface StorerContract
{
    /**
     * Static factory method.
     *
     * @param array $args
     *
     * @return StorerContract
     */
    public static function create(...$args);

    /**
     * Stores a collection of CollectedArticle instances into the database.
     *
     * @param Collection $articles
     *
     * @return int number of successful inserts
     */
    public function store(Collection $articles);
}
