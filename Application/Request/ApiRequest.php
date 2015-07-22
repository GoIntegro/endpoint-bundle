<?php

namespace GoIntegro\Bundle\EndPointBundle\Application\Request;

interface ApiRequest
{
    public function getIncludes($entity = null);
    public function hasIncludes($entity  = null);
    public function getPage();
    public function getSize();
    public function getSort();
    public function getFilter($entity = null);
}
