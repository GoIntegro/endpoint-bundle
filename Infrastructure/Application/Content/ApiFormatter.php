<?php

namespace GoIntegro\Bundle\EndPointBundle\Infrastructure\Application\Content;

use GoIntegro\Bundle\EndPointBundle\Application\Content\Data;
use GoIntegro\Bundle\EndPointBundle\Application\Content\Formatter;
use GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity;
use GoIntegro\Bundle\EndPointBundle\Application\Model\Entity;

/**
 * this is a response with the OLD format from the api
 *
 * @package GoIntegro\Bundle\EndPointBundle\Service
 */
class ApiFormatter implements Formatter
{
    /**
     * return the formatted response from and entity and relationships
     *
     * @param Data $entity
     * @param Data[] $relatedEntities
     * @return array
     */
    public function response(Data $entity, $relatedEntities)
    {
        return [
            $entity->getType() => $entity->toArray(),
            'linked' => $this->getFormattedIncludedData($relatedEntities),
        ];
    }

    /**
     * @param ApiEntity $entity
     * @param $fields
     * @param $includes
     * @return Data
     */
    public function getFormattedEntityData(ApiEntity $entity, $fields, $includes)
    {
        $response = $entity->getData($fields);
        if (!empty($includes)) {
            $response['links'] = [];
            foreach ($includes as $include) {
                $response['links'][$include] = $this->getFormattedRelationshipData($entity, $include);
            }
        }

        return new Data($response);
    }

    /**
     * return the related entity formatted
     *
     * @param ApiEntity $entity
     * @param $include
     * @return array|int|string
     */
    private function getFormattedRelationshipData(ApiEntity $entity, $include)
    {
        $content = $entity->{$include}();
        switch ($entity->getRelationshipsMapperType($include)) {
            case Entity::MAPPER_LINKS_TYPE_UNIQUE:

                return $content->getId();
                break;
            case Entity::MAPPER_LINKS_TYPE_ARRAY:
                $ids = [];
                foreach ($content as $entity) {
                    $ids[] = $entity->getId();
                }

                return $ids;
                break;
            case Entity::MAPPER_LINKS_TYPE_POLYMORPHIC:
                $return = [];
                foreach ($content as $entity) {
                    $return[] = [
                        'id' => $entity->getId(),
                        'type' => $this->normalizeType($entity),
                    ];
                }

                return $return;
                break;
        }

        if (method_exists($content, 'getId')) {
            return $content->getId();
        }

        $ids = [];
        foreach ($content as $entity) {
            $ids[] = $entity->getId();
        }

        return $ids;
    }

    /**
     * normalize the type
     *
     * @param $entity
     * @return string
     */
    private function normalizeType($entity)
    {
        $class = get_class($entity);
        $class = explode('\\', $class);
        $type = array_pop($class);
        $entity = array_pop($class);

        $class = preg_replace(
            '/([a-z])([A-Z])/',
            '\\1-\\2',
            $type
        );

        return strtolower($entity . '/' . $class);
    }

    /**
     * return the included data formatted
     *
     * @param array $linkedData
     * @return array
     */
    private function getFormattedIncludedData(array $linkedData)
    {
        $return = [];
        foreach ($linkedData as $linked) {
            if (!isset($return[$linked->getType()])) {
                $return[$linked->getType()] = [];
            }
            $return[$linked->getType()][] = $linked->toArray();
        }

        return $return;
    }
}
