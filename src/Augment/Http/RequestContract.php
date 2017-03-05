<?php

namespace Profounder\Augment\Http;

interface RequestContract extends \Profounder\Foundation\Http\RequestContract
{
    /**
     * Sets "pidlist" data parameter.
     *
     * @param string $articleContentId
     *
     * @return RequestContract
     */
    public function withArticle($articleContentId);
}
