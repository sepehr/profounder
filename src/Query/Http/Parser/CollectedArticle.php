<?php

namespace Profounder\Query\Http\Parser;

use Profounder\Foundation\Support\Utils;
use Profounder\Foundation\Http\Parser\ParsedObject;

/**
 * Class representing collected article data.
 *
 * @property  int $price
 * @property  string $sku
 * @property  string $date
 * @property  string $title
 * @property  string $publisher
 * @property  string $content_id
 * @property  string $internal_id
 */
class CollectedArticle extends ParsedObject implements CollectedArticleContract
{
    /**
     * CollectedArticle constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct($this->prepareAttributes($attributes));
    }

    /**
     * @inheritdoc
     */
    public function withoutPublisher()
    {
        unset($this->publisher);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function getContentId()
    {
        return $this->content_id;
    }

    /**
     * @inheritdoc
     */
    public function getInternalId()
    {
        return $this->internal_id;
    }

    /**
     * Prepares CollectedArticle attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    private function prepareAttributes(array $attributes)
    {
        return [
            'sku'         => $attributes['Sku'],
            'publisher'   => $attributes['Publisher'],
            'content_id'  => $attributes['ContentId'],
            'internal_id' => $attributes['InternalId'],
            'title'       => Utils::stripTags($attributes['Title']),
            'price'       => Utils::preparePrice($attributes['Price']),
            'date'        => Utils::reformatDate($attributes['DocDateTime']),
        ];
    }
}
