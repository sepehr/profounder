<?php
namespace Profounder\Query;

use Illuminate\Support\Collection;

interface StorerContract
{
    /**
     * Static factory method.
     *
     * @param  array $args
     *
     * @return StorerContract
     */
    public static function create(...$args);

    /**
     * Stores a collection of CollectedArticle objects into the database.
     *
     * @param  Collection $articles
     *
     * @return int Number of successful inserts.
     */
    public function store(Collection $articles);
}
