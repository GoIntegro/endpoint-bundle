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
        $return = null;
        if (isset($this->record['type'])) {
            $return = $this->record['type'];
        }

        if (is_null($return) && isset($this->record[0]['type'])) {
            $return = $this->record[0]['type'];
        }

        return $return;
    }

    public function toArray()
    {
        return $this->record;
    }
}
