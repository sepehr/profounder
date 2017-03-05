<?php

namespace Profounder\Persistence\Repository\Eloquent;

use Profounder\Persistence\Entity\Eloquent\Toc;
use Profounder\Persistence\Repository\TocRepositoryContract;

class TocRepository extends Repository implements TocRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function entity()
    {
        return Toc::class;
    }

    /**
     * Creates a Toc associated with the passed article ID.
     *
     * @param array $toc
     * @param int   $articleId
     *
     * @return bool
     */
    public function createArticleToc(array $toc, $articleId)
    {
        return (bool) $this->entity->create([
            'children'   => $toc,
            'article_id' => $articleId,
        ]);
    }
}
