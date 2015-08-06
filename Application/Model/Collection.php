<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Model;

use Iterator;

class Collection implements Iterator, ApiEntity
{
    /**
     * @var ApiEntity[]
     */
    private $entities = [];

    /**
     * @var int
     */
    private $count = 0;

    public function addEntity(ApiEntity $apiEntity)
    {
        if (in_array($apiEntity, $this->entities, true)) {
            return;
        }

        $this->entities[] = $apiEntity;
    }

    public function removeEntity(ApiEntity $apiEntity)
    {
        $this->entities = array_udiff(
            $this->entities,
            array($apiEntity), function($a, $b) {
                return ($a === $b) ? 0 : 1;
            }
        );
    }

    /**
     * return the type of the resource
     *
     * @return string
     */
    public function getResourceType()
    {
        if (!isset($this->entities[0])) {
            return null;
        }

        return $this->entities[0]->getResourceType();
    }

    /**
     * return the id
     *
     * @return int
     */
    public function getId()
    {
        $ids = [];
        foreach ($this->entities as $entity) {
            $ids[] = $entity->getId();
        }

        return $ids;
    }

    /**
     * return all the attributes that has the entity
     *
     * @param array $fields
     * @return array
     */
    public function getData(array $fields)
    {
        $data = [];
        foreach ($this->entities as $entity) {
            $data[] = $entity->getData($fields);
        }

        return $data;
    }

    /**
     * return which type of relationship is
     *
     * @param $entity
     * @return int|null
     */
    public function getRelationshipsMapperType($entity)
    {
        $mapper = [];
        foreach ($this->entities as $ent) {
            $mapper[] = $ent->getRelationshipsMapperType($entity);
        }

        return $mapper;
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int) $this->count;
    }

    /**
     * @param $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->entities);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->entities);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->entities);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return key($this->entities) !== null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->entities);
    }

    /**
     * magic method to obtain the attributes from the record
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $result = [];
        foreach ($this->entities as $entity) {
            $data = call_user_func_array(array($entity, $name), $arguments);
            foreach ($data as $ent) {
                $result[] = $ent;
            }
        }

        return $result;
    }
}
