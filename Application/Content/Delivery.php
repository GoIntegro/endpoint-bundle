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
     * @param bool $withRelationships
     * @return Data
     */
    private function getEntityData(
        ApiRequest $apiRequest,
        ApiEntity $apiEntity,
        $withRelationships = true
    ) {
        return $this->getFormattedEntityData(
            $apiEntity,
            $apiRequest->getFilter($apiEntity->getResourceType()),
            $withRelationships
        );
    }

    /**
     * return an array of related entities
     *
     * @param ApiRequest $apiRequest
     * @param ApiEntity $apiEntity
     * @param bool $withRelationships
     * @return array
     */
    private function getIncludedData(
        ApiRequest $apiRequest,
        ApiEntity $apiEntity,
        $withRelationships = true
    ) {
        if (!$apiRequest->hasIncludes()) {
            return [];
        }

        $result = [];
        foreach ($apiRequest->getIncludes() as $entityToInclude) {
            $result = $this->getFormattedIncludedData(
                $apiRequest,
                $apiEntity,
                $entityToInclude,
                $result,
                $withRelationships
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
     * @param array $result
     * @param $withRelationships
     * @return array
     */
    private function getFormattedIncludedData(
        ApiRequest $apiRequest,
        ApiEntity $apiEntity,
        $entityToInclude,
        array $result,
        $withRelationships
    ) {
        $entities = $apiEntity->{$entityToInclude}();
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        $toIncludes = $apiRequest->getIncludes($entityToInclude);

        foreach ($entities as $entity) {
            if (!is_a($entity, 'GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity')) {
                continue;
            }

            if (!empty($toIncludes)) {
                foreach ($toIncludes as $toInclude) {
                    $includes = [];
                    $includes = $this->getFormattedIncludedData($apiRequest, $entity, $toInclude, $includes, $withRelationships);
                    if (!empty($includes)) {
                        foreach ($includes as $include) {
                            $exist = false;
                            foreach ($result as $response) {
                                if ($response->isEqual($include)) {
                                    $exist = true;
                                }
                            }

                            if (!$exist) {
                                $result[] = $include;
                            }
                        }
                    }
                }
            }

            $type = $entity->getResourceType();
            $object = null;
            $object = $this->getFormattedEntityData(
                $entity,
                $apiRequest->getFilter($type),
                $withRelationships
            );

            $exist = false;
            foreach ($result as $return) {
                if ($return->isEqual($object)) {
                    $exist = true;
                }
            }

            if (!$exist) {
                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * call the content formatter to get the entity data
     *
     * @param ApiEntity $apiEntity
     * @param $filter
     * @param $withRelationships
     * @return Data
     */
    private function getFormattedEntityData(ApiEntity $apiEntity, $filter, $withRelationships)
    {
        return $this->contentFormatter->getFormattedEntityData($apiEntity, $filter, $withRelationships);
    }
}
