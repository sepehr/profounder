<?php

namespace Profounder\Augment\Http;

interface RequestContract extends \Profounder\Foundation\Http\RequestContract
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
