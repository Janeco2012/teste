<?php

namespace Weserv\Images\Test\Api;

use Jcupitt\Vips\Access;
use Mockery\MockInterface;
use Weserv\Images\Api\Api;
use Weserv\Images\Client;
use Weserv\Images\Manipulators\ManipulatorInterface;
use Weserv\Images\Test\ImagesWeservTestCase;

class ApiTest extends ImagesWeservTestCase
{
    /**
     * @var Client|MockInterface
     */
    private $client;

    /**
     * @var Api
     */
    private $api;

    public function setUp()
    {
        $this->client = $this->getMockery(Client::class);
        $this->api = new Api($this->client, $this->getManipulators());
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Api::class, $this->api);
    }

    public function testSetClient(): void
    {
        $this->api->setClient($this->getMockery(Client::class));
        $this->assertInstanceOf(Client::class, $this->api->getClient());
    }

    public function testGetClient(): void
    {
        $this->assertInstanceOf(Client::class, $this->api->getClient());
    }

    public function testSetManipulators(): void
    {
        $this->api->setManipulators([$this->getMockery(ManipulatorInterface::class)]);
        $manipulators = $this->api->getManipulators();
        $this->assertInstanceOf(ManipulatorInterface::class, $manipulators[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidManipulator(): void
    {
        $this->api->setManipulators([new \StdClass()]);
    }

    public function testGetManipulators(): void
    {
        $this->assertEquals($this->getManipulators(), $this->api->getManipulators());
    }

    public function testGetLoadOptions(): void
    {
        $params = [
            'accessMethod' => Access::SEQUENTIAL,
            'page' => 0,
        ];

        $params['tmpFileName'] = 'test.pdf';
        $params['loader'] = 'VipsForeignLoadPdfFile';
        $this->assertEquals($this->api->getLoadOptions($params), [
            'access' => Access::SEQUENTIAL,
            'page' => 0
        ]);
        $this->assertEquals($params['tmpFileName'], 'test.pdf[page=0]');

        $params['tmpFileName'] = 'test.tiff';
        $params['loader'] = 'VipsForeignLoadTiffFile';
        $this->assertEquals($this->api->getLoadOptions($params), [
            'access' => Access::SEQUENTIAL,
            'page' => 0
        ]);
        $this->assertEquals($params['tmpFileName'], 'test.tiff[page=0]');

        $params['tmpFileName'] = 'test.ico';
        $params['loader'] = 'VipsForeignLoadMagickFile';
        $this->assertEquals($this->api->getLoadOptions($params), [
            'access' => Access::SEQUENTIAL,
            'page' => 0
        ]);
        $this->assertEquals($params['tmpFileName'], 'test.ico[page=0]');

        $params['tmpFileName'] = 'test.jpg';
        $params['loader'] = 'VipsForeignLoadJpegFile';
        $this->assertEquals($this->api->getLoadOptions($params), [
            'access' => Access::SEQUENTIAL
        ]);
        $this->assertEquals($params['tmpFileName'], 'test.jpg');
    }
}
