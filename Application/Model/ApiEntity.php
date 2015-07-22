<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Model;

interface ApiEntity
{
    /**
     * return the type of the resource
     *
     * @return string
     */
    public function getResourceType();

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