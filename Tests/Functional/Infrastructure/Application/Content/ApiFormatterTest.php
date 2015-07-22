<?php

namespace GoIntegro\Bundle\EndPointBundle\Tests\Functional\Service;

use GoIntegro\Bundle\EndPointBundle\Application\Content\Data;
use GoIntegro\Bundle\EndPointBundle\Application\Model\Entity;
use GoIntegro\Bundle\EndPointBundle\Infrastructure\Application\Content\ApiFormatter;

class ApiFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function givenADataWhenGetResponseThenMustReturnTheCorrectFormat()
    {
        // arrange
        $contentEntity = \Mockery::mock(new Data([]));
        $contentEntity->shouldReceive('getType')->once()->andReturn('platform');
        $contentEntity->shouldReceive('toArray')->once()->andReturn(['id' => 5]);

        // act
        $response = (new ApiFormatter())->response($contentEntity, []);

        // assert
        $this->assertEquals(['platform' => ['id' => 5], 'linked' => []], $response);
    }

    /**
     * @test
     */
    public function givenADataAndRelatedEntitiesWhenGetResponseThenMustReturnTheCorrectFormat()
    {
        // arrange
        $contentEntity = \Mockery::mock(new Data([]));
        $contentEntity->shouldReceive('getType')->once()->andReturn('environment');
        $contentEntity->shouldReceive('toArray')->once()->andReturn(['id' => 1]);

        $relatedWidget = \Mockery::mock(new Data([]));
        $relatedWidget->shouldReceive('getType')->times(3)->andReturn('widget');
        $relatedWidget->shouldReceive('toArray')->once()->andReturn(['id' => 2]);

        $relatedPlatform = \Mockery::mock(new Data([]));
        $relatedPlatform->shouldReceive('getType')->times(3)->andReturn('platform');
        $relatedPlatform->shouldReceive('toArray')->once()->andReturn(['id' => 3]);

        $relatedWidgetTwo = \Mockery::mock(new Data([]));
        $relatedWidgetTwo->shouldReceive('getType')->times(2)->andReturn('widget');
        $relatedWidgetTwo->shouldReceive('toArray')->once()->andReturn(['id' => 4]);

        // act
        $response = (new ApiFormatter())->response($contentEntity, [
            $relatedWidget,
            $relatedPlatform,
            $relatedWidgetTwo
        ]);

        // assert
        $this->assertEquals(
            [
                'environment' => ['id' => 1],
                'linked' => [
                    'widget' => [
                        ['id' => 2],
                        ['id' => 4],
                    ],
                    'platform' => [
                        ['id' => 3],
                    ],
                ]
            ],
            $response
        );
    }

    /**
     * @test
     */
    public function givenAnApiEntityWhenGetFormattedEntityDataThenMustReturnTheCorrectFormat()
    {
        // arrange
        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntity->shouldReceive('getData')->with(['fields'])->andReturn(['id' => 1, 'type' => 'platform']);

        // act
        $response = (new ApiFormatter())->getFormattedEntityData($apiEntity, ['fields'], []);

        // assert
        $this->assertInstanceOf('GoIntegro\Bundle\EndPointBundle\Application\Content\Data', $response);
        $this->assertEquals('platform', $response->getType());
        $this->assertEquals(['id' => 1, 'type' => 'platform'], $response->toArray());
    }

    /**
     * @test
     */
    public function givenAnApiEntityWithoutMapperWhenGetFormattedEntityDataWithIncludesThenMustReturnTheCorrectFormat()
    {
        $this->markTestSkipped('has to change the method exist in the code');

        // arrange
        $apiEntityPlatform = \Mockery::mock('GoIntegro\Interfaces\Rest\Resource');
        $apiEntityPlatform->shouldReceive('getId')->andReturn(2);

        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntity->shouldReceive('getData')->andReturn(['id' => 1, 'type' => 'environment']);
        $apiEntity->shouldReceive('platform')->andReturn($apiEntityPlatform);
        $apiEntity->shouldReceive('getRelationshipsMapperType')->andReturn([]);

        // act
        $response = (new ApiFormatter())->getFormattedEntityData($apiEntity, [], ['platform']);

        // assert
        $this->assertEquals(
            [
                'id' => 1,
                'type' => 'environment',
                'links' => [
                    'platform' => 2
                ],
            ],
            $response->toArray()
        );
    }

    /**
     * @test
     */
    public function givenAnApiEntityWithoutMapperAndArrayCollectionRelationshipWhenGetFormattedEntityDataWithIncludesThenMustReturnTheCorrectFormat()
    {
        // arrange
        $apiEntityPlatform = \Mockery::mock('GoIntegro\Interfaces\Rest\Resource');
        $apiEntityPlatform->shouldReceive('getId')->andReturn(2);

        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntity->shouldReceive('getData')->andReturn(['id' => 1, 'type' => 'environment']);
        $apiEntity->shouldReceive('platform')->andReturn([$apiEntityPlatform]);
        $apiEntity->shouldReceive('getRelationshipsMapperType')->andReturn([]);

        // act
        $response = (new ApiFormatter())->getFormattedEntityData($apiEntity, [], ['platform']);

        // assert
        $this->assertEquals(
            [
                'id' => 1,
                'type' => 'environment',
                'links' => [
                    'platform' => [2]
                ],
            ],
            $response->toArray()
        );
    }

    /**
     * @test
     */
    public function givenAnApiEntityWithUniqueMapperWhenGetFormattedEntityDataWithIncludesThenMustReturnTheCorrectFormat()
    {
        // arrange
        $apiEntityPlatform = \Mockery::mock('GoIntegro\Interfaces\Rest\Resource');
        $apiEntityPlatform->shouldReceive('getId')->andReturn(2);

        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntity->shouldReceive('getData')->andReturn(['id' => 1, 'type' => 'environment']);
        $apiEntity->shouldReceive('platform')->andReturn($apiEntityPlatform);
        $apiEntity->shouldReceive('getRelationshipsMapperType')->andReturn(Entity::MAPPER_LINKS_TYPE_UNIQUE);

        // act
        $response = (new ApiFormatter())->getFormattedEntityData($apiEntity, [], ['platform']);

        // assert
        $this->assertEquals(
            [
                'id' => 1,
                'type' => 'environment',
                'links' => [
                    'platform' => 2
                ],
            ],
            $response->toArray()
        );
    }

    /**
     * @test
     */
    public function givenAnApiEntityWithArrayMapperWhenGetFormattedEntityDataWithIncludesThenMustReturnTheCorrectFormat()
    {
        // arrange
        $apiEntityPlatform = \Mockery::mock('GoIntegro\Interfaces\Rest\Resource');
        $apiEntityPlatform->shouldReceive('getId')->andReturn(2);

        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntity->shouldReceive('getData')->andReturn(['id' => 1, 'type' => 'environment']);
        $apiEntity->shouldReceive('platform')->andReturn([$apiEntityPlatform]);
        $apiEntity->shouldReceive('getRelationshipsMapperType')->andReturn(Entity::MAPPER_LINKS_TYPE_ARRAY);

        // act
        $response = (new ApiFormatter())->getFormattedEntityData($apiEntity, [], ['platform']);

        // assert
        $this->assertEquals(
            [
                'id' => 1,
                'type' => 'environment',
                'links' => [
                    'platform' => [2]
                ],
            ],
            $response->toArray()
        );
    }

    /**
     * @test
     */
    public function givenAnApiEntityWithPolymorphicMapperWhenGetFormattedEntityDataWithIncludesThenMustReturnTheCorrectFormat()
    {
        $this->markTestSkipped('change the response type');

        // arrange
        $apiEntityPlatform = \Mockery::mock('GoIntegro\Interfaces\Rest\Resource');
        $apiEntityPlatform->shouldReceive('getId')->andReturn(2);

        $apiEntity = \Mockery::mock('GoIntegro\Bundle\EndPointBundle\Application\Model\ApiEntity');
        $apiEntity->shouldReceive('getData')->andReturn(['id' => 1, 'type' => 'environment']);
        $apiEntity->shouldReceive('platform')->andReturn([$apiEntityPlatform]);
        $apiEntity->shouldReceive('getRelationshipsMapperType')->andReturn(Entity::MAPPER_LINKS_TYPE_POLYMORPHIC);

        // act
        $response = (new ApiFormatter())->getFormattedEntityData($apiEntity, [], ['platform']);

        // assert
        $this->assertSame(
            [
                'id' => 1,
                'type' => 'environment',
                'links' => [
                    'platform' => [
                        [
                            'id' => 2,
                            'type' => 'widget',
                        ]
                    ]
                ],
            ],
            $response->toArray()
        );
    }
}
