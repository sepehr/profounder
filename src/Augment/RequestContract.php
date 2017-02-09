<?php

namespace Profounder\Augment;

interface RequestContract extends \Profounder\RequestContract
{
    /**
     * Sets "pidlist" data parameter.
     *
     * @param  string $articleId
     *
     * @return RequestContract
     */
    public function withArticle($articleId);
}
