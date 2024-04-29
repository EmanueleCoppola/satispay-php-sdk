<?php

namespace Satispay\Services;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
// use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
// use Psr\Http\Message\UploadedFileFactoryInterface;
// use Psr\Http\Message\UriFactoryInterface;
use Satispay\SatispayClient;
use Satispay\SatispayResponse;

class HTTPService extends BaseService {
    /**
     * The base URL, it may vary based on the targeted environment.
     *
     * @var string
     */
    private string $baseUrl;

    /**
     * The HTTP client for making requests.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * The factory for creating PSR-7 requests.
     *
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * The factory for creating PSR-7 responses.
     *
     * @var ResponseFactoryInterface
     */
    // private $responseFactory;

    /**
     * The factory for creating PSR-7 server requests.
     *
     * @var ServerRequestFactoryInterface
     */
    // private $serverRequestFactory;

    /**
     * The factory for creating PSR-7 streams.
     *
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * The factory for creating PSR-7 uploaded files.
     *
     * @var UploadedFileFactoryInterface
     */
    // private $uploadedFileFactory;

    /**
     * The factory for creating PSR-7 URIs.
     *
     * @var UriFactoryInterface
     */
    // private $uriFactory;

    /**
     * Default headers for HTTP requests.
     *
     * @var array<string, string>
     */
    const DEFAULT_HEADERS = [
        'Accept' => 'application/json',
    ];

    /**
     * HTTPService constructor.
     *
     * @param SatispayClient $context The SatispayClient context for the service.
     * @param string $baseUrl The base URL for the HTTP service.
     * @param array $psr An array of PSR implementations to override defaults.
     */
    public function __construct($context, $baseUrl, $psr = [])
    {
        $this->baseUrl = $baseUrl;

        $this->context = $context;

        $this->httpClient = $this->discoverImplementation(ClientInterface::class, $psr, Psr18ClientDiscovery::class, 'find');
        $this->requestFactory = $this->discoverImplementation(RequestFactoryInterface::class, $psr, Psr17FactoryDiscovery::class, 'findRequestFactory');
        // $this->responseFactory = $this->discoverImplementation(ResponseFactoryInterface::class, $psr, Psr17FactoryDiscovery::class, 'findResponseFactory');
        // $this->serverRequestFactory = $this->discoverImplementation(ServerRequestFactoryInterface::class, $psr, Psr17FactoryDiscovery::class, 'findServerRequestFactory');
        $this->streamFactory = $this->discoverImplementation(StreamFactoryInterface::class, $psr, Psr17FactoryDiscovery::class, 'findStreamFactory');
        // $this->uploadedFileFactory = $this->discoverImplementation(UploadedFileFactoryInterface::class, $psr, Psr17FactoryDiscovery::class, 'findUploadedFileFactory');
        // $this->uriFactory = $this->discoverImplementation(UriFactoryInterface::class, $psr, Psr17FactoryDiscovery::class, 'findUriFactory');
    }

    /**
     * Resolves and returns an implementation of the PSR factory.
     *
     * This method provides flexibility for users to override default PSR implementations with their preferred choices.
     *
     * @return mixed
     */
    private function discoverImplementation($interface, $implementations, $discoveryClass, $discoveryMethod)
    {
        return
            array_key_exists($interface, $implementations)
                ? $implementations[$interface]
                : forward_static_call([$discoveryClass, $discoveryMethod]);
    }

    /**
     * Make an HTTP request using the specified method, path, body, and headers.
     *
     * @param string $method The HTTP method (e.g., GET, POST).
     * @param string $path The path portion of the URL.
     * @param mixed $body The request body.
     * @param bool $signed Indicates whether the request should be signed.
     * @param array $headers Additional headers for the request.
     *
     * @return ResponseInterface
     */
    public function request(string $method, string $path, $body = null, bool $signed = true, array $headers = [])
    {
        $url = $this->baseUrl . $path;

        $request = $this->requestFactory->createRequest($method, $url);
        $headers = array_merge(self::DEFAULT_HEADERS, $this->context->getHeaders(), $headers);

        if (is_null($body)) {
            $body = '';
        }

        if (is_array($body)) {
            $body = json_encode($body);
            $headers['Content-Type'] = 'application/json';
        }

        if ($signed === true) {
            $headers = $this->signedHeaders($url, $path, $method, $body, $headers);
        }

        $request = $request->withBody(
            $this->streamFactory->createStream($body)
        );

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $this->httpClient->sendRequest($request);
    }

    /**
     * Generate signed headers for Satispay G Business Signature Protocol.
     *
     * @param string $url The URL of the HTTP request.
     * @param string $path The path portion of the URL.
     * @param string $method The HTTP method (e.g., GET, POST).
     * @param string $body The body of the HTTP request.
     * @param array $headers The request headers.
     *
     * @return array
     */
    private function signedHeaders(string $url, string $path, string $method, string $body, array $headers = [])
    {
        // these headers will be explicitely sent by the HTTP client as headers
        // they will be used for both signature and the request
        // $headers = []; <- inherited from the parameter

        // these headers will not be explicitely sent by the client
        // they will be only used for signature generation
        $signatureHeaders         = [];
        $signatureHeadersSequence = [];

        $signatureHeaders['(request-target)'] = strtolower($method) . ' ' . $path;
        $signatureHeaders['Host'] = parse_url($url, PHP_URL_HOST);

        $headers['Date'] = date('r');
        $headers['Digest'] = 'SHA-256=' . base64_encode(hash('sha256', $body, true));

        if (!empty($body)) {
            $headers['Content-Type'] = 'application/json';
            $headers['Content-Length'] = strlen($body);
        }

        $signature = [];

        foreach([...$headers, ...$signatureHeaders] as $header => $value) {
            $signature[]                = mb_strtolower($header) . ': ' . $value;
            $signatureHeadersSequence[] = $header;
        }

        $signature = implode(PHP_EOL, $signature);

        // -- authorization
        $base64SignedSignature = $this->context->authentication->sign($signature);

        if ($base64SignedSignature) {
            $base64SignedSignature = base64_encode($base64SignedSignature);
        }

        $signatureHeadersSequence = implode(' ', $signatureHeadersSequence);

        $headers['Authorization'] = sprintf(
            'Signature keyId="%s", algorithm="rsa-sha256", headers="%s", signature="%s"',
            $this->context->authentication->keyId,
            $signatureHeadersSequence,
            $base64SignedSignature
        );

        return $headers;
    }

    /**
     * Build a URL-encoded query string from an associative array, allowing boolean values to be represented as 'true' or 'false'.
     *
     * This function is an alternative implementation of the native PHP http_build_query function.
     * It converts boolean values in the input array to string representations ('true' or 'false').
     *
     * @param array $data The associative array of parameters.
     * @param string $numeric_prefix If numeric indices are used in the base array and this parameter is provided, it will be prepended to the numeric index for elements in the base array only.
     * @param string|null $arg_separator The argument separator to use. Default is '&'.
     * @param int $encoding_type An optional constant specifying how to encode spaces. Default is PHP_QUERY_RFC1738.
     *
     * @return string
     */
    private function http_build_query($data, string $numeric_prefix = '', $arg_separator = null, $encoding_type = PHP_QUERY_RFC1738)
    {
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $data[$key] = $value ? 'true' : 'false';
            }
        }

        return http_build_query($data, $numeric_prefix, $arg_separator, $encoding_type);
    }

    /**
     * Make an HTTP POST request.
     *
     * @param string $path The path portion of the URL.
     * @param array|null $body The request body.
     * @param bool $signed Indicates whether the request should be signed.
     * @param array $headers Additional headers for the request.
     *
     * @return SatispayResponse
     */
    public function post($path, $body, $signed = true, $headers = [])
    {
        return new SatispayResponse(
            $this->request('POST', $path, $body, $signed, $headers),
            $this->context
        );
    }

    /**
     * Make an HTTP GET request.
     *
     * @param string $path The path portion of the URL.
     * @param array $query The query parameters for the request.
     * @param bool $signed Indicates whether the request should be signed.
     * @param array $headers Additional headers for the request.
     *
     * @return SatispayResponse
     */
    public function get($path, $query = [], $signed = true, $headers = [])
    {
        if (!empty($query)) {
            $path .= '?' . $this->http_build_query($query);
        }

        return new SatispayResponse(
            $this->request('GET', $path, null, $signed, $headers),
            $this->context
        );
    }

    /**
     * Make an HTTP PUT request.
     *
     * @param string $path The path portion of the URL.
     * @param array|null $body The request body.
     * @param bool $signed Indicates whether the request should be signed.
     * @param array $headers Additional headers for the request.
     *
     * @return SatispayResponse
     */
    public function put($path, $body, $signed = true, $headers = [])
    {
        return new SatispayResponse(
            $this->request('PUT', $path, $body, $signed, $headers),
            $this->context
        );
    }

    /**
     * Make an HTTP PATCH request.
     *
     * @param string $url The URL of the HTTP request.
     * @param array|null $body The request body.
     * @param bool $signed Indicates whether the request should be signed.
     * @param array $headers Additional headers for the request.
     *
     * @return SatispayResponse
     */
    public function patch($url, $body, $signed = true, $headers = [])
    {
        return new SatispayResponse(
            $this->request('PATCH', $url, $body, $signed, $headers),
            $this->context
        );
    }
}
