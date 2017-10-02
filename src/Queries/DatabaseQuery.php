<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Queries;

use Carbon\Carbon;
use Throwable;
use Traversable;

/**
 * Class DatabaseQuery
 *
 * Profiled query object.
 */
class DatabaseQuery extends AbstractQuery
{
    /**
     * Query content (sql string).
     *
     * @var string|null
     */
    protected $query_content;

    /**
     * Connection name.
     *
     * @var string|null
     */
    protected $connection_name;

    /**
     * Query bindings values array.
     *
     * @var array|null
     */
    protected $bindings;

    /**
     * Query duration time (in micro-seconds).
     *
     * @var float|null
     */
    protected $duration;

    /**
     * {@inheritdoc}
     */
    public function __construct($query_data = [])
    {
        if ($query_data instanceof Traversable || is_array($query_data)) {
            foreach ($query_data as $key => $query_datum) {
                switch ($key) {
                    case 'sql':
                    case 'query':
                    case 'query_content':
                        $this->setQueryContent($query_datum);
                        break;

                    case 'bindings':
                        $this->setBindings($query_datum);
                        break;

                    case 'duration':
                        $this->setDuration($query_datum);
                        break;

                    case 'connection_name':
                    case 'connection':
                        $this->setConnectionName($query_datum);
                        break;

                    case 'when':
                    case 'timestamp':
                    case 'time':
                    case 'datetime':
                        $this->setWhen($query_datum);
                        break;
                }
            }
        }

        if (empty($this->getWhen())) {
            $this->setWhen(Carbon::now());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return (
            !empty($this->getQueryContent())
            && !empty($this->getDuration())
            && !empty($this->getWhen())
        );
    }

    /**
     * Get query content.
     *
     * @return null|string
     */
    public function getQueryContent()
    {
        return $this->query_content;
    }

    /**
     * Set query string.
     *
     * @param string $query_content
     *
     * @return $this
     */
    public function setQueryContent($query_content)
    {
        if (is_string($query_content) && !empty($query_content)) {
            $this->query_content = $query_content;
        }

        return $this;
    }

    /**
     * Get query duration time (in micro-seconds).
     *
     * @return float|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set query duration time (in micro-seconds).
     *
     * @param float|int $duration
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = (float) $duration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'duration'        => $this->getDuration(),
            'query_content'   => $this->getQueryContent() . $this->getQueryContent() . $this->getQueryContent(),
            'connection_name' => $this->getConnectionName(),
            'bindings'        => $this->getBindings(),
            'when'            => $this->getWhen(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toPrintableArray()
    {
        $as_array = $this->toArray();

        $as_array['bindings'] = is_array($as_array['bindings'])
            ? implode('; ', $as_array['bindings'])
            : null;

        $as_array['when'] = $as_array['when'] instanceof Carbon
            ? $as_array['when']->toDateTimeString()
            : null;

        return $as_array;
    }

    /**
     * Get connection name.
     *
     * @return null|string
     */
    public function getConnectionName()
    {
        return $this->connection_name;
    }

    /**
     * Set connection name.
     *
     * @param string $connection_name
     *
     * @return $this
     */
    public function setConnectionName($connection_name)
    {
        if (is_string($connection_name) && !empty($connection_name)) {
            $this->connection_name = $connection_name;
        }

        return $this;
    }

    /**
     * Get query bindings.
     *
     * @return array|null
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Set query bindings.
     *
     * @param array $bindings
     *
     * @return $this
     */
    public function setBindings($bindings)
    {
        try {
            $bindings = (array) $bindings;

            if (is_array($bindings)) {
                $this->bindings = $bindings;
            }
        } catch (Throwable $e) {
            // Do nothing
        }

        return $this;
    }
}
