<?php

namespace GoIntegro\Bundle\EndPointBundle\Tests\Functional\Request;

use GoIntegro\Bundle\EndPointBundle\Infrastructure\Application\Request\HttpRequest;
use Symfony\Component\HttpFoundation\Request;

class HttpRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function givenNoIncludeWhenHasIncludesThenMustReturnFalse()
    {
        // arrange
        $request = new Request();

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertFalse($apiRequest->hasIncludes());
    }

    /**
     * @test
     */
    public function givenAnIncludeWhenHasIncludeThenMustReturnTrue()
    {
        // arrange
        $request = new Request([
            'include' => true
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertTrue($apiRequest->hasIncludes());
    }

    /**
     * @test
     */
    public function givenAnIncludeWhenHasIncludeAnEntityThenMustReturnTrue()
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ]
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertTrue($apiRequest->hasIncludes('platform'));
    }

    /**
     * @test
     */
    public function givenAnIncludeWhenHasIncludeAnEntityThatNoExistThenMustReturnFalse()
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ]
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertFalse($apiRequest->hasIncludes('platforms'));
    }

    /**
     * @test
     */
    public function givenNotIncludesWhenGetIncludesThenMustReturnEmpty()
    {
        // arrange
        $request = new Request();

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getIncludes();
        $this->assertEquals([], $array);
    }

    /**
     * @test
     */
    public function givenIncludesWhenGetIncludesThatNotExistThenMustReturnEmpty()
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ]
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getIncludes('environment');
        $this->assertEquals([], $array);
    }

    /**
     * @test
     */
    public function givenIncludesWhenGetIncludesThenMustReturnTheDataWithoutChilds()
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ]
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getIncludes();
        $this->assertEquals(['platform'], $array);
    }

    /**
     * @test
     */
    public function givenIncludesWhenGetIncludesWithEntityThenMustReturnTheDataChilds()
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ]
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getIncludes('platform');
        $this->assertEquals(['default-language', 'environments'], $array);
    }

    /**
     * @test
     */
    public function givenIncludesAsStringWhenGetIncludesWithEntityThenMustReturnTheDataChilds()
    {
        // arrange
        $request = new Request([
            'include' => 'platform,platform.default-language,platform.environments',
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getIncludes('platform');
        $this->assertEquals(['default-language', 'environments'], $array);
    }

    /**
     * @test
     */
    public function givenNoPageWhenGetPageThenMustReturnZero()
    {
        // arrange
        $request = new Request();

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertEquals(0, $apiRequest->getPage());
    }

    /**
     * @test
     */
    public function givenAPageWhenGetPageThenMustReturnIt()
    {
        // arrange
        $request = new Request(['page' => 5]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertEquals(4, $apiRequest->getPage());
    }

    /**
     * @test
     */
    public function givenASizeWhenGetSizeThenMustReturnIt()
    {
        // arrange
        $request = new Request(['size' => 5]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertEquals(5, $apiRequest->getSize());
    }

    /**
     * @test
     * @dataProvider sortedData
     */
    public function givenASortWhenGetSortThenMustReturnIt($field, $sort)
    {
        // arrange
        $request = new Request(['sort' => $field]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $this->assertEquals(['id' => $sort], $apiRequest->getSort());
    }

    public function sortedData()
    {
        return [
            ['id', 'ASC'],
            ['-id', 'DESC'],
        ];
    }

    /**
     * @test
     */
    public function givenNoSortWhenGetSortThenMustReturnEmptyArray()
    {
        // act
        $apiRequest = new HttpRequest(new Request());

        // arrange
        $this->assertEquals([], $apiRequest->getSort());
    }

    /**
     * @test
     */
    public function givenAFieldWhenGetFilterThenMustReturnIt()
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ],
            'fields' => [
                'widgets' => [
                    'disable',
                    'enabled',
                    'name',
                    'size-x',
                    'size-y',
                ],
            ]
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getFilter();
        $this->assertEquals(['widgets' => ['disable', 'enabled', 'name', 'size-x', 'size-y']], $array);
    }

    /**
     * @test
     * @dataProvider widgetData
     */
    public function givenAFieldWhenGetFilterWithEntityThenMustReturnIt($widgets)
    {
        // arrange
        $request = new Request([
            'include' => [
                'platform',
                'platform.default-language',
                'platform.environments',
            ],
            'fields' => $widgets
        ]);

        // act
        $apiRequest = new HttpRequest($request);

        // arrange
        $array = $apiRequest->getFilter('widgets');
        $this->assertEquals(['disable', 'enabled', 'name', 'size-x', 'size-y'], $array);
    }

    public function widgetData()
    {
        return [
            [['widgets' => ['disable', 'enabled', 'name', 'size-x', 'size-y']]],
            [['widgets' => 'disable,enabled,name,size-x,size-y']],
        ];
    }
}
