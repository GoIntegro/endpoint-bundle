# Endpoint Bundle

Es un bundle para generar contenido y formatearlo dada una entidad.

  - Application: aquí se encuentra la lógica de la aplicación
  - Infrastructure: aquí se encuentra las implementaciones de la aplicación que depende de la infraestructura

### Modo de uso 

Basicamente la clase `Delivery` debe recibir una `ApiRequest` y una `ApiEntity` (previamente `Delivery` tiene injectado un `Formatter` para generar el output.

### Ejemplo

En un caso de uso de **ViewEnvironmentRequest**, es decir, una request para ver la información de la entidad **Environment**, el código quedaría algo así:

```php
<?php

namespace GoIntegro\Bundle\RestApiBundle\Service\Environment;

use GoIntegro\Bundle\EndPointBundle\Application\Content\Delivery;
use GoIntegro\Bundle\EndPointBundle\Application\Request\ApiRequest;
use GoIntegro\Repository\Environment\EnvironmentRepository;

class ViewEnvironmentRequest
{
    /**
     * @var EnvironmentRepository
     */
    private $environmentRepository;
    
    /**
     * @var Delivery
     */
    private $delivery;

    /**
     * @param EnvironmentRepository $environmentRepository
     * @param Delivery $delivery
     */
    public function __construct(
        EnvironmentRepository $environmentRepository,
        Delivery $delivery
    ) {
        $this->environmentRepository = $environmentRepository;
        $this->delivery = $delivery;
    }

    /**
     * @param $environmentId
     * @param ApiRequest $request
     * @return array
     */
    public function execute($environmentId, ApiRequest $request)
    {
        $environment = $this->environmentRepository->findById($environmentId);

        return $this->delivery->generate($request, $environment);
    }
}

```
  
Por lo que la aplicación que implemente este *endopoint-bundle* debé tener sus propias **ApiEntity** y repositorios que las generen.

### Formatter
En caso de querer cambiar el formato de respuesta, en ```Infrastructura\Application\Content``` hay que generar un nuevo formatter e inyectar este en ```Application\Content\Delivery```

Los dos métodos que hay que implementar de ```Application\Content\Formatter``` son para formato general y para cada entidad ```response``` y ```getFormattedEntityData``` respectivamente
