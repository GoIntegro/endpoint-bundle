<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Content;

/**
 * class that has a common interface, the formatter knows how to work with this
 */
class Data
{
    /**
     * @var array
     */
    private $record;

    public function __construct(array $record)
    {
        $this->record = $record;
    }

    public function getType()
    {
        return isset($this->record['type']) ? $this->record['type'] : $this->record[0]['type'];
    }

    public function toArray()
    {
        return $this->record;
    }
}
