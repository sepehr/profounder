<?php
namespace Profounder\Augment\Augmentor;

use Profounder\Augment\Http\Parser\ArticlePageContract;

interface AugmentorContract
{
    /**
     * Augments an article by ID from an ArticlePage instance.
     *
     * @param  string  $articleContentId
     * @param  ArticlePageContract  $articlePage
     *
     * @return bool
     *
     * @throws \RuntimeException
     * @throws \Profounder\Exception\InvalidArgument
     */
    public function augment($articleContentId, ArticlePageContract $articlePage);
}
