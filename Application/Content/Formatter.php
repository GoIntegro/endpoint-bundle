<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Content;

use GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity;

/**
 * all the class that worry only about the structure of the response must implement
 * this class
 *
 * @package GoIntegro\Bundle\EndPointBundle\Service
 */
interface Formatter
{
    /**
     * return the formatted response from and entity and relationships
     *
     * @param Data $entity
     * @param Data[] $relatedEntities
     * @return array
     */
    public function response(Data $entity, $relatedEntities);

    /**
     * @param ApiEntity $entity
     * @param $fields
     * @param $includes
     * @return Data
     */
    public function getFormattedEntityData(ApiEntity $entity, $fields, $includes);
}
