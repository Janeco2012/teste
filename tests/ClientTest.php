<?php

namespace Weserv\Images\Test;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Weserv\Images\Client;

class ClientTest extends ImagesWeservTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $tempFile;

    /**
     * @var array
     */
    private $options;

    public function setUp()
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'phpunit');
        $this->options = [
            'user_agent' => 'Mozilla/5.0 (compatible; ImageFetcher/6.0; +http://images.weserv.nl/)',
            'connect_timeout' => 5,
            'timeout' => 10,
            'max_image_size' => 0,
            'max_redirects' => 10,
            'allowed_mime_types' => []
        ];
        $this->client = new Client($this->tempFile, $this->options);
    }

    public function tearDown()
    {
        parent::tearDown();
        unlink($this->tempFile);
    }

    public function testCreateInstance(): void
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    public function testSetClient(): void
    {
        $client = $this->getMockery(GuzzleClient::class);
        $this->client->setClient($client);
        $this->assertInstanceOf(GuzzleClient::class, $this->client->getClient());
    }

    public function testGetClient(): void
    {
        $this->assertInstanceOf(GuzzleClient::class, $this->client->getClient());
    }

    public function testGetOptions(): void
    {
        $this->assertSame($this->options, $this->client->getOptions());
    }

    /**
     * @expectedException \GuzzleHttp\Exception\RequestException
     */
    public function testInvalidRedirectURI(): void
    {
        $this->client->get('http://test');
    }

    public function testUserAgent(): void
    {
        $mock = new MockHandler([
            new Response(200)
        ]);

        $handler = HandlerStack::create($mock);

        $history = [];
        $handler->push(Middleware::history($history));

        $this->client = new Client($this->tempFile, $this->options, ['handler' => $handler]);
        $this->client->get('image.jpg');

        $this->assertSame($this->options['user_agent'], end($history)['request']->getHeaderLine('User-Agent'));
    }

    public function testTempFile(): void
    {
        $mock = new MockHandler([
            new Response(200)
        ]);

        $handler = HandlerStack::create($mock);

        $this->client = new Client($this->tempFile, $this->options, ['handler' => $handler]);
        $this->client->get('image.jpg');

        $this->assertSame($this->client->getFileName(), $mock->getLastOptions()['sink']);
    }
}
