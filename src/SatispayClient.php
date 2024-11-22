<?php

namespace EmanueleCoppola\Satispay;

use Composer\InstalledVersions;
use EmanueleCoppola\Satispay\Contracts\RSAServiceContract;
use EmanueleCoppola\Satispay\Exceptions\SatispayRSAException;
use EmanueleCoppola\Satispay\Services\AuthenticationService;
use EmanueleCoppola\Satispay\Services\HTTPService;
use EmanueleCoppola\Satispay\Services\RSAService\OpenSSL_RSAService;
use EmanueleCoppola\Satispay\Services\RSAService\SeclibRSAService;

/**
 * Class SatispayClient
 *
 * A PHP client for interacting with the Satispay API, providing an abstraction for all the functionality.
 */
class SatispayClient {

    /**
     * Default configuration for the SatispayClient class.
     *
     * @var array<string, mixed>
     */
    private const DEFAULT_CONFIG = [
        // authentication
        'public_key' => null,
        'private_key' => null,
        'key_id' => null,

        'passphrase' => null,

        // MQTT authentication
        'mqtt_client_certificate' => null,
        'mqtt_client_certificate_key' => null,
        'mqtt_shop_uid' => null,

        //
        'sandbox' => false,

        //
        'rsa_service' => [
            OpenSSL_RSAService::class,
            SeclibRSAService::class
        ],

        // HTTP
        'psr' => [],
        'headers' => []
    ];

    /**
     * Configuration options for the SatispayClient instance.
     *
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * HTTP service instance for handling API requests.
     *
     * @var HTTPService
     */
    public HTTPService $http;

    /**
     * Authentication service instance for managing authentication with the Satispay API.
     *
     * @var AuthenticationService
     */
    public AuthenticationService $authentication;

    /**
     * RSA service instance for managing RSA keys.
     *
     * @var RSAServiceContract
     */
    public RSAServiceContract $rsa_service;

    //
    const STAGING_BASE_URL = 'https://staging.authservices.satispay.com';
    const PRODUCTION_BASE_URL = 'https://authservices.satispay.com';

    //
    const LIBRARY_NAME = 'emanuelecoppola/satispay-php-sdk';

    /**
     * SatispayClient constructor.
     *
     * @param array<string, mixed> $config Configuration options for the client.
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge(
            self::DEFAULT_CONFIG,
            [
                'headers' => [
                    SatispayHeaders::USER_AGENT => self::LIBRARY_NAME . '/' . $this->version() . ' PHP/' . phpversion(),

                    SatispayHeaders::OS => php_uname('s'),
                    SatispayHeaders::OS_VERSION => php_uname('r') . ' ' . php_uname('v') . ' ' . php_uname('m')
                ]
            ],
            $config
        );

        $this->rsa_service = $this->resolveRSAService(
            $this->config['rsa_service'],
            $this->config['public_key'],
            $this->config['private_key'],
            $this->config['passphrase']
        );

        $this->authentication = new AuthenticationService($this, $this->config['key_id']);

        $this->http = new HTTPService(
            $this,
            $this->sandbox() ? self::STAGING_BASE_URL : self::PRODUCTION_BASE_URL,
            $this->config['psr']
        );

        $this->boot($this->config);
    }

    /**
     * Function that instantiate all the dependencies.
     *
     * @param mixed $config The configuration data required for bootstrapping dependencies.
     *
     * @return void
     */
    protected function boot(array $config): void {}

    /**
     * Retrieve the package version installed.
     *
     * @return string
     */
    protected function version(): string
    {
        return InstalledVersions::getPrettyVersion(self::LIBRARY_NAME);
    }

    /**
     * Resolve the RSA service to be used.
     *
     * By default:
     * OpenSSL will be used if the OpenSSL extension is available.
     * The phpseclib will be used if found in the composer.json.
     *
     * You can also create your own implementation by implementing the RSAServiceContract.
     *
     * @throws SatispayRSAException if non of the services is available.
     *
     * @return RSAServiceContract
     */
    private function resolveRSAService(
        $RSAServices,
        $publicKey = null,
        $privateKey = null,
        $passphrase = null
    ): RSAServiceContract
    {
        $RSAService = (array) $RSAServices;

        foreach($RSAService as $RSAservice) {

            /** @var RSAServiceContract */
            $RSAserviceInstance = new $RSAservice($publicKey, $privateKey, $passphrase);

            if ($RSAserviceInstance->isAvailable()) {
                return $RSAserviceInstance;
            }
        }

        throw new SatispayRSAException('No RSA Service available. Either install the OpenSSL PHP extension or the phpseclib 3 composer library.');
    }

    /**
     * Get the current configuration settings for the SatispayClient.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the default SatispayClient headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        if (key_exists('headers', $this->config)) {
            return $this->config['headers'];
        }

        return [];
    }

    /**
     * Check if the client is running in sandbox.
     *
     * @return bool
     */
    public function sandbox(): bool
    {
        return $this->config['sandbox'] === true;
    }

    /**
     * Make an HTTP POST request.
     *
     * @param string $path The path portion of the URL.
     * @param array|null $body The request body.
     * @param array $headers Additional headers for the request.
     * @param bool $signed Indicates whether the request should be signed.
     *
     * @return SatispayResponse
     */
    public function post(string $path, array|null $body, array $headers = [], bool $signed = true): SatispayResponse
    {
        $response = $this->http->post($path, $body, $headers, $signed);

        $response->checkExceptions();

        return $response;
    }

    /**
     * Make an HTTP GET request.
     *
     * @param string $path The path portion of the URL.
     * @param array $query The query parameters for the request.
     * @param array $headers Additional headers for the request.
     * @param bool $signed Indicates whether the request should be signed.
     *
     * @return SatispayResponse
     */
    public function get(string $path, array $query = [], array $headers = [], bool $signed = true): SatispayResponse
    {
        $response = $this->http->get($path, $query, $headers, $signed);

        $response->checkExceptions();

        return $response;
    }

    /**
     * Make an HTTP PUT request.
     *
     * @param string $path The path portion of the URL.
     * @param array|null $body The request body.
     * @param array $headers Additional headers for the request.
     * @param bool $signed Indicates whether the request should be signed.
     *
     * @return SatispayResponse
     */
    public function put(string $path, array|null $body, array $headers = [], $signed = true)
    {
        $response = $this->http->put($path, $body, $headers, $signed);

        $response->checkExceptions();

        return $response;
    }

    /**
     * Make an HTTP PATCH request.
     *
     * @param string $path The path portion of the URL.
     * @param array|null $body The request body.
     * @param array $headers Additional headers for the request.
     * @param bool $signed Indicates whether the request should be signed.
     *
     * @return SatispayResponse
     */
    public function patch(string $path, array $body, array $headers = [], bool $signed = true): SatispayResponse
    {
        $response = $this->http->patch($path, $body, $headers, $signed);

        $response->checkExceptions();

        return $response;
    }
}
