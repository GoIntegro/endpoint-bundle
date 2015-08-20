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

    public function getId()
    {
        return $this->getRecord('id');
    }

    public function getType()
    {
        return $this->getRecord('type');
    }

    public function getSubType()
    {
        return $this->getRecord('subtype');
    }

    public function isEqual(Data $data)
    {
        return $data->getId() === $this->getId() &&
           $data->getType() === $this->getType() &&
           $data->getSubType() === $this->getSubType()
        ;
    }

    private function getRecord($field)
    {
        $return = null;
        if (isset($this->record[$field])) {
            $return = $this->record[$field];
        }

        if (is_null($return) && isset($this->record[0][$field])) {
            $return = $this->record[0][$field];
        }

        return $return;
    }

    public function toArray()
    {
        return $this->record;
    }
}
