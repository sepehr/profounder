<?php

namespace Profounder\Query;

use Carbon\Carbon;
use Profounder\Exceptions\InvalidArgument;

class Builder
{
    const PRICE = 'price';
    const DATE = 'docdatetime';
    const RELEVANCE = 'mrdclongfalloffextrafresh';

    /**
     * Query parameters array.
     *
     * @var array
     */
    private $params = [
        'limit'   => 5,
        'offset'  => 0,
        'keyword' => '',
        'date'    => null,
        'order'   => 'desc',
        'sort'    => self::DATE,
    ];

    /**
     * Date format acceptable by remote search endpoint.
     *
     * @var string
     */
    private $dateFormat = 'Y-m-d';

    /**
     * Query constructor.
     *
     * @param  array $params
     */
    public function __construct(array $params = [])
    {
        $this->initialize($params);
    }

    /**
     * Static factory method.
     *
     * @param  array $params
     *
     * @return Builder
     */
    public static function create(array $params = [])
    {
        return new static($params);
    }

    /**
     * Builds the query in one go.
     *
     * @param  array $params
     *
     * @return string
     */
    public static function buildFromArray(array $params)
    {
        return self::create($params)->build();
    }

    /**
     * Initializes class properties.
     *
     * @param  array $params
     *
     * @return Builder
     */
    public function initialize(array $params)
    {
        if (isset($params['date'])) {
            $this->setDate($params['date']);

            unset($params['date']);
        }

        $this->params = array_replace($this->params, $params);

        return $this;
    }

    /**
     * Sets query keyword.
     *
     * @param  string $keyword
     *
     * @return Builder
     */
    public function searchFor($keyword)
    {
        $this->params['keyword'] = $keyword;

        return $this;
    }

    /**
     * Sets query order.
     *
     * @param  string $field
     * @param  string $direction Accepted values are: asc, desc
     *
     * @return Builder
     */
    public function orderBy($field, $direction = 'desc')
    {
        $this->params['sort']  = $field;
        $this->params['order'] = $direction;

        return $this;
    }

    /**
     * Sets query offset.
     *
     * @param  int $offset
     *
     * @return Builder
     */
    public function offset($offset)
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    /**
     * Sets query limit.
     *
     * @param  int $limit
     *
     * @return Builder
     */
    public function take($limit)
    {
        $this->params['limit'] = $limit;

        return $this;
    }

    /**
     * Sets query date condition.
     *
     * @param  Carbon $start
     * @param  Carbon|null $end
     *
     * @return Builder
     */
    public function byDate(Carbon $start, Carbon $end = null)
    {
        $this->params['date'] = $this->prepareDate($start, $end);

        return $this;
    }

    /**
     * Sets query date condition string.
     *
     * @param  string $date Format: {start-date-string-1}[{$glue}{end-date-string}]
     *
     * @return Builder
     *
     * @throws InvalidArgument
     */
    public function byDateString($date)
    {
        if (empty($date)) {
            return $this;
        }

        if (strpos($date, ',')) {
            $date = array_map(function ($date) {
                return new Carbon(trim($date));
            }, explode(',', $date));
        } else {
            $date = [new Carbon($date), null];
        }

        return $this->byDate(...$date);
    }

    /**
     * Slightly more flexible date setter.
     *
     * @param  string|array|Carbon $date
     *
     * @return Builder
     *
     * @throws InvalidArgument
     */
    public function setDate($date)
    {
        if (is_string($date)) {
            return $this->byDateString($date);
        }

        if ($date instanceof Carbon) {
            return $this->byDate($date);
        }

        if (is_array($date) && $date[0] instanceof Carbon) {
            return $this->byDate($date[0], $date[1]);
        }

        throw new InvalidArgument('Invalid date parameter.');
    }

    /**
     * Sets default date format.
     *
     * @param  $format
     *
     * @return Builder
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * Builds the querystring.
     *
     * @return string
     */
    public function build()
    {
        $query = [
            'sortby'      => $this->params['sort'],
            'sortorder'   => $this->params['order'],
            'hits'        => $this->params['limit'],
            'offset'      => $this->params['offset'],
            'querystring' => $this->params['keyword'],
        ];

        if (! empty($this->params['date'])) {
            $query['filter'] = "<>docdatetime,{$this->params['date']}";
        }

        return http_build_query($query);
    }

    /**
     * Prepares date parameters for the query.
     *
     * @param  Carbon $start
     * @param  Carbon|null $end
     *
     * @return string
     */
    private function prepareDate($start, $end = null)
    {
        return $end
            ? $start->format($this->dateFormat) . ',' . $end->format($this->dateFormat)
            : $start->format($this->dateFormat) . ',MAX';
    }
}
