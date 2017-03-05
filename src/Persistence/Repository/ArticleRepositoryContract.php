<?php

namespace Profounder\Persistence\Repository;

interface ArticleRepositoryContract extends RepositoryContract
{
    /**
     * Finds an article by its content ID.
     *
     * @param  string $contentId
     *
     * @return \Profounder\Persistence\Entity\ArticleContract
     */
    public function findByContentId($contentId);

    /**
     * Checks whether the article with the passed content ID exists or not.
     *
     * @param  string $contentId
     *
     * @return bool
     */
    public function existsByContentId($contentId);

    /**
     * Queries for non-augmented articles which reside between the passed IDs and executes the callback against them.
     *
     * @param  int  $minId
     * @param  int  $maxId
     * @param  int  $chunk
     * @param  callable  $callback
     *
     * @return void
     */
    public function executeOnNonAugmentedWithin($minId, $maxId, $chunk, $callback);

    /**
     * Returns article ID by the passed offset.
     *
     * @param  int $offset
     *
     * @return int
     */
    public function getNonAugmentedIdByOffset($offset);

    /**
     * Returns a list of available SKUs.
     *
     * @return string
     */
    public function dumpSku();

    /**
     * Returns a formatted string of all articles in CSV.
     *
     * @return string
     */
    public function dumpCsv();
}
