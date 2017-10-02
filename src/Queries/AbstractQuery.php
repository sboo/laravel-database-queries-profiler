<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Queries;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Traversable;

/**
 * Class AbstractQuery
 *
 * Profiled query object.
 */
abstract class AbstractQuery implements Arrayable, Jsonable
{
    /**
     * When query was executed.
     *
     * @var Carbon|null
     */
    protected $when;

    /**
     * DatabaseQuery constructor.
     *
     * @param array|Traversable $query_data
     */
    abstract public function __construct($query_data = []);

    /**
     * Make self validation.
     *
     * @return bool
     */
    abstract public function isValid();

    /**
     * Get the instance as an printable (with scalar values only) array.
     *
     * @return array
     */
    abstract public function toPrintableArray();

    /**
     * Get unique query hash.
     *
     * @return string
     */
    public function getUniqueHash()
    {
        return sprintf('query_hash_%s', crc32(serialize($this->toArray())));
    }

    /**
     * Get 'when' value.
     *
     * @return Carbon|null
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * Set 'when' value.
     *
     * @param Carbon|DateTime|int|string $when
     *
     * @return $this
     */
    public function setWhen($when)
    {
        $this->when = $this->anyToCarbon($when);

        return $this;
    }

    /**
     * Convert any value to the carbon object.
     *
     * @param Carbon|DateTime|int|string $value
     * @param null|string                $date_format
     *
     * @return Carbon|null
     */
    protected function anyToCarbon($value, $date_format = null)
    {
        try {
            if ($value instanceof Carbon) {
                return $value;
            } elseif ($value instanceof DateTime) {
                return Carbon::instance($value);
            } elseif (is_int($value) && $value > 0) {
                return Carbon::createFromTimestamp($value);
            } else {
                return empty($date_format)
                    ? Carbon::parse($value)
                    : Carbon::createFromFormat($date_format, $value);
            }
        } catch (Exception $e) {
            // Do nothing
        }

        return null;
    }
}
