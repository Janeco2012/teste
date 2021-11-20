<?php

namespace Weserv\Images;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Weserv\Images\Exception\ImageNotValidException;
use Weserv\Images\Exception\ImageTooBigException;
use Weserv\Images\Manipulators\Helpers\Utils;

class Client
{
    /**
     * Temp file name to download to
     *
     * @var string
     */
    protected $fileName;

    /**
     * Options for this client
     *
     * @var array
     */
    protected $options;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @param string $fileName Temp file name to download to
     * @param mixed[] $options Client options
     * @param mixed[] $guzzleOptions Specific Guzzle options
     */
    public function __construct(string $fileName, array $options, array $guzzleOptions = [])
    {
        $this->fileName = $fileName;
        $this->setOptions($options);
        $this->initClient($guzzleOptions);
    }

    /**
     * Initialize the client
     *
     * @param mixed[] $guzzleOptions Specific Guzzle options
     *
     * @return void
     */
    private function initClient(array $guzzleOptions): void
    {
        $defaultConfig = [
            'connect_timeout' => $this->options['connect_timeout'],
            'decode_content' => true,
            'verify' => false,
            'allow_redirects' => [
                'max' => $this->options['max_redirects'], // allow at most 10 redirects.
                'strict' => false,      // use "strict" RFC compliant redirects.
                'referer' => true,      // add a Referer header
                'track_redirects' => false
            ],
            'expect' => false, // Send an empty Expect header (avoids 100 responses)
            'http_errors' => true,
            'on_headers' => function (ResponseInterface $response) {
                if (!empty($this->options['allowed_mime_types']) &&
                    !isset($this->options['allowed_mime_types'][$response->getHeaderLine('Content-Type')])) {
                    $supportedImages = array_pop($this->options['allowed_mime_types']);
                    if (\count($this->options['allowed_mime_types']) > 1) {
                        $supportedImages = implode(', ', $this->options['allowed_mime_types']) .
                            ' and ' . $supportedImages;
                    }
                    $error = 'The request image is not a valid (supported) image. Supported images are: ' .
                        $supportedImages;
                    throw new ImageNotValidException($error);
                }
                if ($this->options['max_image_size'] !== 0 &&
                    $response->getHeaderLine('Content-Length') > $this->options['max_image_size']) {
                    $size = (int)$response->getHeaderLine('Content-Length');
                    throw new ImageTooBigException(Utils::formatBytes($size));
                }
            }
        ];

        $guzzleConfig = array_merge($defaultConfig, $guzzleOptions);
        $guzzleClient = new GuzzleClient($guzzleConfig);

        $this->setClient($guzzleClient);
    }

    /**
     * Create client instance.
     *
     * @param GuzzleClient $client The guzzle client.
     *
     * @return void
     */
    public function setClient(GuzzleClient $client): void
    {
        $this->client = $client;
    }

    /**
     * Get the client instance.
     *
     * @return GuzzleClient $client The guzzle client.
     */
    public function getClient(): GuzzleClient
    {
        return $this->client;
    }

    /**
     * Set the client options
     *
     * @param mixed[] $options Client options
     *
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Get the client options
     *
     * @return mixed[] $options Client options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param  string $url
     *
     * @throws ImageNotValidException if the requested image is not a valid
     *      image.
     * @throws ImageTooBigException if the requested image is too big to be
     *      downloaded.
     * @throws GuzzleException for errors that occur during a transfer
     *      or during the on_headers event.
     * @throws \InvalidArgumentException if the redirect URI can not be
     *      parsed (with parse_url).
     * @throws \Throwable if the requested image is too big or not valid.
     *
     * @return string File name
     */
    public function get(string $url): string
    {
        $requestOptions = [
            'sink' => $this->fileName,
            'timeout' => $this->options['timeout'],
            'headers' => [
                'Accept-Encoding' => 'gzip',
                'User-Agent' => $this->options['user_agent']
            ]
        ];

        /**
         * @var ResponseInterface $response
         */
        try {
            $this->client->request('GET', $url, $requestOptions);
        } catch (GuzzleException $e) {
            $previousException = $e->getPrevious();

            // Check if we need to throw a previous exception
            // which can occur during the on_headers event
            if ($previousException instanceof ImageNotValidException ||
                $previousException instanceof ImageTooBigException) {
                throw $previousException;
            }

            // Still here? Then just re-throw the exception.
            throw $e;
        }

        return $this->fileName;
    }
}
