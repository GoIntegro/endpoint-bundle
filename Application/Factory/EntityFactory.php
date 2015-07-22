<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Factory;

use GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity;

interface EntityFactory
{
    /**
     * @param $resource
     * @return ApiEntity[]
     */
    public function get($resource);
}