<?php
namespace Profounder\Persistence\Repository;

interface TocRepositoryContract extends RepositoryContract
{
    /**
     * Creates a Toc associated with the passed article ID.
     *
     * @param array $toc
     * @param int   $articleId
     *
     * @return bool
     */
    public function createArticleToc(array $toc, $articleId);
}
