<?php

/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 17:41
 */
class YamlApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestApi
     */
    protected $resource;

    protected function setUp()
    {
        parent::setUp();

        $response = Mockery::mock();
        $response->shouldReceive('getBody')->andReturn(file_get_contents(__DIR__ . '/stub/index.html'));

        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->andReturn($response);

        $this->resource = new \SleepingOwl\Apist\Yaml\YamlApist($client, '', __DIR__ . '/stub/habr.yml');
    }

    /** @test */
    public function it_parses_result_by_blueprint()
    {
        $result = $this->resource->posts_page();

        $this->assertEquals('Моя лента', $result['title']);
        $this->assertEquals('http://tmtm.ru/', $result['copyright']);
        $this->assertCount(10, $result['posts']);
        $this->assertEquals('Проверьте своего хостера на'.
            ' уязвимость Shellshock (часть 2)', $result['posts'][0]['title']);
    }
}