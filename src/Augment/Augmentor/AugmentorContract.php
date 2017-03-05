<?php
namespace Profounder\Augment\Augmentor;

use Profounder\Augment\Http\Parser\ArticlePageContract;

interface AugmentorContract
{
    /**
     * Augments an article by ID from an ArticlePage instance.
     *
     * @param string              $articleContentId
     * @param ArticlePageContract $articlePage
     *
     * @throws \RuntimeException
     * @throws \Profounder\Exception\InvalidArgument
     *
     * @return bool
     */
    public function augment($articleContentId, ArticlePageContract $articlePage);
}
