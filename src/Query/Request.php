<?php

namespace Profounder\Query;

use Profounder\Request as AbstractRequest;

class Request extends AbstractRequest implements RequestContract
{
    /**
     * @inheritdoc
     */
    protected $method = 'post';

    /**
     * @inheritdoc
     */
    protected $uri = 'http://www.profound.com/home/FilterSearchResults';

    /**
     * @inheritdoc
     */
    protected $headers = [
        'X-Requested-With' => 'XMLHttpRequest',
        'Referer'          => 'http://www.profound.com/home/search',
        'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
    ];

    /**
     * @inheritdoc
     */
    protected $data = [
        'SearchFilter'      => '',
        'HasUsedNavFilters' => 'false',
        'searchMethod'      => '/home/FilterSearchResults',
    ];

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        return $this->withData('SearchFiler', $query);
    }
}
