<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Model;

use Countable;

interface ApiEntity extends Countable
{
    /**
     * return the type of the resource
     *
     * @return string
     */
    public function getResourceType();

    /**
     * return the id
     *
     * @return int
     */
    public function getId();

    /**
     * return all the attributes that has the entity
     *
     * @param array $fields
     * @return array
     */
    public function getData(array $fields);

    /**
     * return which type of relationship is
     *
     * @param $entity
     * @return int|null
     */
    public function getRelationshipsMapperType($entity);
}