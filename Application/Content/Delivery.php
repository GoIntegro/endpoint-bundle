<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Content;

use GoIntegro\Bundle\EndPointBundle\Application\Factory\EntityFactory;
use GoIntegro\Bundle\EndPointBundle\Application\Request\ApiRequest;
use GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity;

/**
 * This is the entry point, here is where will start given an entity and a
 * formatter to give the response to the endpoint
 */
class Delivery
{
    /**
     * @var Formatter
     */
    private $contentFormatter;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @param Formatter $contentFormatter
     * @param EntityFactory $entityFactory
     */
    public function __construct(Formatter $contentFormatter, EntityFactory $entityFactory)
    {
        $this->contentFormatter = $contentFormatter;
        $this->entityFactory = $entityFactory;
    }

    /**
     * return the structure and content that need the request
     *
     * @param ApiRequest $apiRequest
     * @param ApiEntity $apiEntity
     * @return array
     */
    public function generate(ApiRequest $apiRequest, ApiEntity $apiEntity)
    {
        return $this->contentFormatter->response(
            $this->getEntityData($apiRequest, $apiEntity),
            $this->getIncludedData($apiRequest, $apiEntity)
        );
    }

    /**
     * obtain the data that need for an entity
     *
     * @param ApiRequest $apiRequest
     * @param ApiEntity $apiEntity
     * @return Data
     */
    private function getEntityData(ApiRequest $apiRequest, ApiEntity $apiEntity)
    {
        return $this->getFormattedEntityData(
            $apiEntity,
            $apiRequest->getFilter($apiEntity->getResourceType()),
            $apiRequest->getIncludes()
        );
    }

    /**
     * call the content formatter to get the entity data
     *
     * @param ApiEntity $apiEntity
     * @param $filter
     * @param $include
     * @return Data
     */
    private function getFormattedEntityData(ApiEntity $apiEntity, $filter, $include)
    {
        return $this->contentFormatter->getFormattedEntityData($apiEntity, $filter, $include);
    }

    /**
     * return an array of related entities
     *
     * @param ApiRequest $apiRequest
     * @param ApiEntity $apiEntity
     * @return array
     */
    private function getIncludedData(ApiRequest $apiRequest, ApiEntity $apiEntity)
    {
        if (!$apiRequest->hasIncludes()) {
            return [];
        }

        $result = [];
        foreach ($apiRequest->getIncludes() as $entityToInclude) {
            $result = $this->getFormattedIncludedData(
                $apiRequest,
                $apiEntity,
                $entityToInclude,
                $result
            );
        }

        return $result;
    }

    /**
     * @param ApiRequest $apiRequest
     * @param ApiEntity $apiEntity
     * @param $entityToInclude
     * @param $result
     * @return array
     */
    private function getFormattedIncludedData(
        ApiRequest $apiRequest,
        ApiEntity $apiEntity,
        $entityToInclude,
        array $result
    ) {
        $entities = $this->getIncludedEntities($apiEntity, $entityToInclude);
        foreach ($entities as $entity) {
            $type = $entity->getResourceType();
            $result[] = $this->getFormattedEntityData($entity, $apiRequest->getFilter($type), $apiRequest->getIncludes($entityToInclude));
        }

        return $result;
    }

    /**
     * @param ApiEntity $entity
     * @param $included
     *
     * @return ApiEntity[]
     */
    private function getIncludedEntities(ApiEntity $entity, $included)
    {
        $content = $entity->{$included}();

        return $this->entityFactory->get($content);
    }
}
