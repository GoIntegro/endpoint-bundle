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
     * @param array|\GoIntegro\Bundle\EndPointBundle\Application\Content\Data[] $includedEntities
     * @param array $extra
     * @return array
     */
    public function response(Data $entity, array $includedEntities, array $extra = [])
    {
        $response[$entity->getType()] = $entity->toArray();
        if (!empty($includedEntities)) {
            $response['linked'] = $this->getFormattedIncludedData($includedEntities);
        }

        if (!empty($extra)) {
            $response['meta'] = $extra;
        }

        return $response;
    }

    /**
     * @param ApiEntity $entity
     * @param $fields
     * @param bool $withRelationships
     * @return Data
     */
    public function getFormattedEntityData(ApiEntity $entity, $fields, $withRelationships = true)
    {
        if ( ! is_a($entity, 'GoIntegro\Bundle\EndPointBundle\Application\Model\Collection')) {
            $response = $entity->getData($fields);
            if ($withRelationships && $entity->hasRelationships()) {
                $response['links'] = [];
                foreach ($entity->getRelationships() as $relationships) {
                    $response['links'][$relationships] = $this->getFormattedRelationshipData($entity, $relationships);
                }
            }
        } else {
            $response = [];
            foreach ($entity as $ent) {
                $response[] = $this->getFormattedEntityData($ent, $fields, $withRelationships)->toArray();
            }
        }

        return new Data($response);
    }

    /**
     * return the included data formatted
     *
     * @param Data[] $linkedData
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

    /**
     * return the related entity formatted
     *
     * @param ApiEntity $entity
     * @param $include
     * @return array|int|string
     */
    public function getFormattedRelationshipData(ApiEntity $entity, $include)
    {
        $content = $entity->{$include}();

        switch ($entity->getRelationshipsMapperType($include)) {
            case Entity::MAPPER_LINKS_TYPE_UNIQUE:
                if (empty($content)) {
                    return null;
                }

                return $content->getId();
                break;
            case Entity::MAPPER_LINKS_TYPE_ARRAY:
                if (empty($content)) {
                    return [];
                }

                $ids = [];
                foreach ($content as $entity) {
                    $ids[] = $entity->getId();
                }

                return $ids;
                break;
            case Entity::MAPPER_LINKS_TYPE_POLYMORPHIC:
                if (empty($content)) {
                    return [];
                }

                $return = [];
                foreach ($content as $entity) {
                    $type = $entity->getResourceSubType();
                    $return[] = [
                        'id' => $entity->getId(),
                        'type' => $type ?: $this->normalizeType($entity),
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
}
