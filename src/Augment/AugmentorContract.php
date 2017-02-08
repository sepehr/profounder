<?php
namespace Profounder\Augment;

interface AugmentorContract
{
    /**
     * Augments an article by ID from an ArticlePage instance.
     *
     * @param  string $articleId Article content ID.
     * @param  ArticlePage $articlePage
     *
     * @return bool
     *
     * @throws \RuntimeException
     * @throws \Profounder\Exception\InvalidArgument
     */
    public function augment($articleId, ArticlePage $articlePage);
}
