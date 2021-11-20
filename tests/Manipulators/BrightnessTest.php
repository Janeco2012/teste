<?php

namespace Weserv\Images\Test\Manipulators;

use Jcupitt\Vips\Image;
use Mockery\MockInterface;
use Weserv\Images\Api\Api;
use Weserv\Images\Client;
use Weserv\Images\Manipulators\Brightness;
use Weserv\Images\Test\ImagesWeservTestCase;

class BrightnessTest extends ImagesWeservTestCase
{
    /**
     * @var Client|MockInterface
     */
    private $client;

    /**
     * @var Api
     */
    private $api;

    /**
     * @var Brightness
     */
    private $manipulator;

    public function setUp()
    {
        $this->client = $this->getMockery(Client::class);
        $this->api = new Api($this->client, $this->getManipulators());
        $this->manipulator = new Brightness();
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Brightness::class, $this->manipulator);
    }

    public function testBrightnessIncrease(): void
    {
        $testImage = $this->inputJpg;
        $expectedImage = $this->expectedDir . '/brightness-increase.jpg';
        $params = [
            'w' => '320',
            'h' => '240',
            't' => 'square',
            'bri' => '30'
        ];

        $uri = basename($testImage);

        $this->client->shouldReceive('get')->with($uri)->andReturn($testImage);

        /** @var Image $image */
        $image = $this->api->run($uri, $params);

        $this->assertEquals(320, $image->width);
        $this->assertEquals(240, $image->height);
        $this->assertSimilarImage($expectedImage, $image);
    }

    public function testBrightnessDecrease(): void
    {
        $testImage = $this->inputJpg;
        $expectedImage = $this->expectedDir . '/brightness-decrease.jpg';
        $params = [
            'w' => '320',
            'h' => '240',
            't' => 'square',
            'bri' => '-30'
        ];

        $uri = basename($testImage);

        $this->client->shouldReceive('get')->with($uri)->andReturn($testImage);

        /** @var Image $image */
        $image = $this->api->run($uri, $params);

        $this->assertEquals(320, $image->width);
        $this->assertEquals(240, $image->height);
        $this->assertSimilarImage($expectedImage, $image);
    }

    public function testBrightnessPngTransparent(): void
    {
        $testImage = $this->inputPngOverlayLayer1;
        $expectedImage = $this->expectedDir . '/brightness-trans.png';
        $params = [
            'w' => '320',
            'h' => '240',
            't' => 'square',
            'bri' => '30'
        ];

        $uri = basename($testImage);

        $this->client->shouldReceive('get')->with($uri)->andReturn($testImage);

        /** @var Image $image */
        $image = $this->api->run($uri, $params);

        $this->assertEquals(320, $image->width);
        $this->assertEquals(240, $image->height);
        $this->assertSimilarImage($expectedImage, $image);
    }

    public function testGetBrightness(): void
    {
        $this->assertSame(50, $this->manipulator->setParams(['bri' => '50'])->getBrightness());
        $this->assertSame(50, $this->manipulator->setParams(['bri' => 50])->getBrightness());
        $this->assertSame(0, $this->manipulator->setParams(['bri' => null])->getBrightness());
        $this->assertSame(0, $this->manipulator->setParams(['bri' => '101'])->getBrightness());
        $this->assertSame(0, $this->manipulator->setParams(['bri' => '-101'])->getBrightness());
        $this->assertSame(0, $this->manipulator->setParams(['bri' => 'a'])->getBrightness());
    }
}
