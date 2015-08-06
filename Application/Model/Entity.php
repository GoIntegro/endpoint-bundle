<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Model;

abstract class Entity
{
    const MAPPER_LINKS_TYPE_UNIQUE = 1;
    const MAPPER_LINKS_TYPE_ARRAY = 2;
    const MAPPER_LINKS_TYPE_POLYMORPHIC = 3;

    public function count()
    {
        return 1;
    }
}
