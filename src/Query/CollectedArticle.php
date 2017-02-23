<?php

namespace Profounder\Query;

use Profounder\Utils;
use Illuminate\Support\Fluent;

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
class CollectedArticle extends Fluent
{
    /**
     * CollectedArticle constructor.
     *
     * @param  array $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct($this->prepareAttributes($attributes));
    }

    /**
     * Static factory method.
     *
     * @param  array $args
     *
     * @return $this
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * Prepares CollectedArticle attributes.
     *
     * @param  array $attributes
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
