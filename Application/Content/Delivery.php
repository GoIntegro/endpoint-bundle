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
     * @param Formatter $contentFormatter
     */
    public function __construct(Formatter $contentFormatter)
    {
        $this->contentFormatter = $contentFormatter;
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
            $this->getIncludedData($apiRequest, $apiEntity),
            $this->getMeta($apiRequest, $apiEntity)
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

    private function getMeta(ApiRequest $apiRequest, ApiEntity $apiEntity)
    {
        return [
            'pagination' => [
                'count' => count($apiEntity),
                'page' => $apiRequest->getPage() + 1,
                'size' => $apiRequest->getSize(),
            ],
        ];
    }

    /**
     * formatted each entity include in the request
     *
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
        $entities = $apiEntity->{$entityToInclude}();
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        foreach ($entities as $entity) {
            if (!is_a($entity, 'GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity')) {
                continue;
            }

            $type = $entity->getResourceType();
            $result[] = $this->getFormattedEntityData(
                $entity,
                $apiRequest->getFilter($type),
                $apiRequest->getIncludes($entityToInclude)
            );
        }

        return $result;
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
}
