<?php

namespace GoIntegro\Bundle\EndPointBundle\Infrastructure\Application\Request;

use GoIntegro\Bundle\EndPointBundle\Application\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class HttpRequest implements ApiRequest
{
    /**
     * @var HttpFoundationRequest
     */
    private $request;

    const DEFAULT_LIMIT = 50;

    public function __construct(HttpFoundationRequest $request)
    {
        $this->request = $request;
    }

    public function getIncludes($entity = null)
    {
        if (!$this->request->query->has('include')) {
            return [];
        }

        $include = $this->request->query->get('include');
        if(!is_array($include)) {
            $include = explode(",", $include);
        }

        $result = [];
        foreach ($include as $value) {
            $values = explode('.', $value);
            if (empty($entity)) {
                if (count($values) === 1) {
                    $result[] = $value;
                }
            } else {
                if (count($values) > 1) {
                    if ($values[0] === $entity) {
                        $result[] = $values[1];
                    }
                }
            }
        }

        return $result;
    }

    public function hasIncludes($entity = null)
    {
        if (!$entity) {
            return $this->request->query->has('include');
        }

        $includes = $this->getIncludes($entity);

        return !empty($includes);
    }

    public function getPage()
    {
        return $this->request->query->has('page')
            ? (int) $this->request->query->get('page') - 1
            : 0
            ;
    }

    public function getSize()
    {
        return $this->request->query->has('size')
            ? (int) $this->request->query->get('size')
            : self::DEFAULT_LIMIT
            ;
    }

    public function getSort()
    {
        if (!$this->request->query->has('sort')) {
            return [];
        }

        $response = [];
        $sort = $this->request->query->get('sort');
        if(!is_array($sort)) {
            $sort = explode(",", $sort);
        }
        foreach($sort as $field) {
            if ('-' != substr($field, 0, 1)) {
                $order = 'ASC';
            } else {
                $order = 'DESC';
                $field = substr($field, 1);
            }
            $response[$field] = $order;
        }

        return $response;
    }

    public function getFilter($entity = null)
    {
        $response = [];
        foreach ($this->request->query as $param => $value) {
            if (!in_array($param, array("include", "page", "size", "sort", "access_token"))) {
                foreach ($value as $key => $field) {
                    $response[$key] = !is_array($field) ? explode(',' , $field) : $field;
                }
            }
        }

        return !empty($entity) && isset($response[$entity])
            ? $response[$entity]
            : $response
            ;
    }
}
