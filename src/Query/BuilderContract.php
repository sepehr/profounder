<?php
namespace Profounder\Query;

use Carbon\Carbon;

interface BuilderContract
{
    const PRICE     = 'price';
    const DATE      = 'docdatetime';
    const RELEVANCE = 'mrdclongfalloffextrafresh';

    /**
     * Static factory method.
     *
     * @param  array $args
     *
     * @return BuilderContract
     */
    public static function create(...$args);

    /**
     * Builds the query in one go.
     *
     * @param  array $params
     *
     * @return string
     */
    public static function buildFromArray(array $params);

    /**
     * Initializes class properties.
     *
     * @param  array $params
     *
     * @return BuilderContract
     */
    public function initialize(array $params);

    /**
     * Sets query keyword.
     *
     * @param  string $keyword
     *
     * @return BuilderContract
     */
    public function searchFor($keyword);

    /**
     * Sets query order.
     *
     * @param  string $field
     * @param  string $direction Accepted values are: asc, desc
     *
     * @return BuilderContract
     */
    public function orderBy($field, $direction = 'desc');

    /**
     * Sets query offset.
     *
     * @param  int $offset
     *
     * @return BuilderContract
     */
    public function offset($offset);

    /**
     * Sets query limit.
     *
     * @param  int $limit
     *
     * @return BuilderContract
     */
    public function take($limit);

    /**
     * Sets query date condition.
     *
     * @param  Carbon $start
     * @param  Carbon|null $end
     *
     * @return BuilderContract
     */
    public function byDate(Carbon $start, Carbon $end = null);

    /**
     * Sets query date condition string.
     *
     * @param  string $date Format: {start-date-string-1}[{$glue}{end-date-string}]
     *
     * @return BuilderContract
     *
     * @throws \Profounder\Exception\InvalidArgument
     */
    public function byDateString($date);

    /**
     * Slightly more flexible date setter.
     *
     * @param  string|array|Carbon $date
     *
     * @return BuilderContract
     *
     * @throws \Profounder\Exception\InvalidArgument
     */
    public function setDate($date);

    /**
     * Sets default date format.
     *
     * @param  $format
     *
     * @return BuilderContract
     */
    public function setDateFormat($format);

    /**
     * Builds the querystring.
     *
     * @return string
     */
    public function build();
}
