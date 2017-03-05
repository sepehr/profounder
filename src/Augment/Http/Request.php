<?php

namespace Profounder\Augment\Http;

use Profounder\Auth\Http\AuthenticatedRequest;

class Request extends AuthenticatedRequest implements RequestContract
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
    public function withArticle($articleContentId)
    {
        return $this->withData('pidlist', $articleContentId);
    }
}
