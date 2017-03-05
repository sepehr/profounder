<?php

namespace Profounder\Persistence\Entity;

interface PublisherContract extends EntityContract
{
    /**
     * Creates an associated article.
     *
     * @param array $article
     *
     * @return \Profounder\Persistence\Entity\ArticleContract
     */
    public function createArticle(array $article);
}
