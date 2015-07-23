<?php

namespace GoIntegro\Bundle\EndPointBundle\Tests\Functional\Service;

use GoIntegro\Bundle\EndPointBundle\Application\Content\Data;
use GoIntegro\Bundle\EndPointBundle\Application\Content\Delivery;
use GoIntegro\Bundle\EndPointBundle\Infrastructure\Application\Request\HttpRequest;
use Symfony\Component\HttpFoundation\Request;

class DeliveryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    private $formatter;

    /**
     * @var Delivery
     */
    private $sut;

    public function setUp()
    {
        parent::setUp();
        $this->formatter = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Content\Formatter');
        $this->sut = new Delivery($this->formatter);
    }

    public function tearDown()
    {
        $this->formatter = null;
        $this->sut = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function givenAnApiRequestWithoutIncludesWhenGenerateThenMustCallTheFormatter()
    {
        // arrange
        $apiRequest = new HttpRequest(new Request());
        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $content = new Data(['id' => 1, 'type' => 'environment']);

        // assert
        $apiEntity->shouldReceive('getResourceType')->once()->andReturn('environments');
        $this->formatter->shouldReceive('getFormattedEntityData')
                        ->with($apiEntity, [], [])
                        ->andReturn($content);
        $this->formatter->shouldReceive('response')->once()->with($content, []);

        // act
        $this->sut->generate($apiRequest, $apiEntity);
    }

    /**
     * @test
     * @dataProvider includesData
     */
    public function givenAnApiRequestWithIncludesWhenGenerateThenMustCallTheFormatter($includes)
    {
        // arrange
        $apiRequest = new HttpRequest(new Request(['include' => ['platform']]));

        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntityPlatform = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $contentEnvironment = new Data(['id' => 1, 'type' => 'environment']);
        $contentPlatform = new Data(['id' => 2, 'type' => 'platform']);

        // assert
        $apiEntity->shouldReceive('getResourceType')->once()->andReturn('environments');
        $apiEntity->shouldReceive('platform')->once()->andReturn(
            ($includes == 1)
                ? $apiEntityPlatform
                : [$apiEntityPlatform]
        );
        $apiEntityPlatform->shouldReceive('getResourceType')->once()->andReturn('platforms');

        $this->formatter->shouldReceive('getFormattedEntityData')
            ->once()
            ->with($apiEntity, [], ['platform'])
            ->andReturn($contentEnvironment);

        $this->formatter->shouldReceive('getFormattedEntityData')
            ->once()
            ->with($apiEntityPlatform, [], [])
            ->andReturn($contentPlatform);

        $this->formatter->shouldReceive('response')->once()->with($contentEnvironment, [$contentPlatform]);

        // act
        $this->sut->generate($apiRequest, $apiEntity);
    }

    public function includesData()
    {
        return [
            [1],
            [2],
        ];
    }
}
