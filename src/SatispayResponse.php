<?php

namespace EmanueleCoppola\Satispay;

use Psr\Http\Message\ResponseInterface;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\Exceptions\UnsupportedMediaTypeException;

/**
 * Class SatispayResponse
 *
 * The Satispay response abstraction with all the response data extraction methods.
 */
class SatispayResponse {

    /**
     * The HTTP response object.
     *
     * @var ResponseInterface
     */
    public $response;

    /**
     * The client that created the request.
     *
     * @var SatispayClient
     */
    private $context;

    /**
     * SatispayResponse constructor.
     */
    public function __construct(
        ResponseInterface $response,
        SatispayClient $context
    )
    {
        $this->response = $response;
        $this->context = $context;
    }

    /**
     * Check for specified exceptions during construction.
     *
     * @param array $exceptions An array of exception class names to be checked.
     *
     * @throws UnsupportedMediaTypeException
     */
    public function checkExceptions($exceptions = [])
    {
        // Example
        //
        // $exceptions = [
        //     400 => [
        //         21 => "This is an error message"
        //     ]
        // ];

        if (key_exists($this->getStatusCode(), $exceptions))
        {
            if (is_string($exceptions[$this->getStatusCode()])) {
                throw new SatispayResponseException(
                    $this,
                    $exceptions[$this->getStatusCode()]
                );
            } else if (key_exists($this->getErrorCode(), $exceptions[$this->getStatusCode()])) {
                throw new SatispayResponseException(
                    $this,
                    $exceptions[$this->getStatusCode()][$this->getErrorCode()]
                );
            }
        }

        $this->checkSharedExceptions();
    }

    /**
     * Check shared exceptions that are the same for every request.
     *
     * @throws SatispayResponseException
     */
    protected function checkSharedExceptions()
    {
        $statusCode = $this->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new SatispayResponseException(
                $this,
                "[{$statusCode}] - {$this->getMessage()}"
            );
        }
    }

    /**
     * Get the HTTP status code from the response.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get the headers from the response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * Retrieve the execution environment in which the request has taken place.
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->context->sandbox() ? 'sandbox' : 'production';
    }

    /**
     * Get the CID header from the response.
     *
     * @return string|null
     */
    public function getCID()
    {
        $headers = $this->response->getHeaders();

        $header = 'x-satispay-cid';

        if (
            key_exists($header, $headers) &&
            count($headers[$header]) > 0
        ) {
            return $headers[$header][0];
        }

        return null;
    }

    /**
     * Get the error code from the response.
     *
     * @return int
     */
    public function getErrorCode()
    {
        $response = $this->toArray();

        if (key_exists('code', $response)) {
            return $response['code'];
        }

        return -1;
    }

    /**
     * Get the error message from the response.
     *
     * @return string
     */
    public function getMessage()
    {
        $response = $this->toArray();

        if (key_exists('message', $response)) {
            return $response['message'];
        }

        return 'Error';
    }

    /**
     * Get the original response object.
     *
     * @return ResponseInterface
     */
    public function toResponse()
    {
        return $this->response;
    }

    /**
     * Get the response body as JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        return $this->response->getBody();
    }

    /**
     * Get the response body as an associative array.
     *
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * Get the response body as an object.
     *
     * @return object
     */
    public function toObject()
    {
        return json_decode($this->response->getBody());
    }
}
