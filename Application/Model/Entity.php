<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Model;

abstract class Entity
{
    const MAPPER_LINKS_TYPE_UNIQUE = 1;
    const MAPPER_LINKS_TYPE_ARRAY = 2;
    const MAPPER_LINKS_TYPE_POLYMORPHIC = 3;

    /**
     * return the type of the resource
     *
     * @return string
     */
    abstract public function getResourceType();

    /**
     * return all the attributes that has the entity
     *
     * @param array $fields
     * @return array
     */
    abstract public function getData(array $fields);

    /**
     * return which type of relationship is
     *
     * @param $entity
     * @return int|null
     */
    abstract public function getRelationshipsMapperType($entity);
}