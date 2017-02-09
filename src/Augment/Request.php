<?php

namespace Profounder\Augment;

use Profounder\Request as AbstractRequest;

class Request extends AbstractRequest implements RequestContract
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
     * @inheritdoc
     */
    public function withArticle($articleId)
    {
        return $this->withData('pidlist', $articleId);
    }
}
