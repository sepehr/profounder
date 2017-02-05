<?php

namespace Profounder\Augment;

use Profounder\Request as AbstractRequest;

class Request extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $method = 'get';

    /**
     * @inheritdoc
     */
    protected $uri = 'http://www.profound.com/Pages/Search/MultipleItemDetailPage.aspx';

    /**
     * Sets "pidlist" data parameter.
     *
     * @param  string $articleId
     *
     * @return $this
     */
    public function withArticle($articleId)
    {
        return $this->withData('pidlist', $articleId);
    }
}
