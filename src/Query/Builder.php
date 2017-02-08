<?php

namespace Profounder\Query;

use Carbon\Carbon;
use Profounder\Exception\InvalidArgument;

class Builder implements BuilderContract
{
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
     * @inheritdoc
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * @inheritdoc
     */
    public static function buildFromArray(array $params)
    {
        return self::create($params)->build();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function searchFor($keyword)
    {
        $this->params['keyword'] = $keyword;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orderBy($field, $direction = 'desc')
    {
        $this->params['sort']  = $field;
        $this->params['order'] = $direction;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function offset($offset)
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function take($limit)
    {
        $this->params['limit'] = $limit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function byDate(Carbon $start, Carbon $end = null)
    {
        $this->params['date'] = $this->prepareDate($start, $end);

        return $this;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * @inheritdoc
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
