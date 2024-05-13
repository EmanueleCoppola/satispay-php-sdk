<?php

namespace Satispay;

use Satispay\Services\AuthenticationService;
use Satispay\Services\HTTPService;

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

        //
        'sandbox' => false,

        // HTTP
        'psr' => [],
        'headers' => []
    ];

    /**
     * Configuration options for the SatispayClient instance.
     *
     * @var array<string, mixed>
     */
    private $config;

    /**
     * HTTP service instance for handling API requests.
     *
     * @var HTTPService
     */
    public $http;

    /**
     * Authentication service instance for managing authentication with the Satispay API.
     *
     * @var AuthenticationService
     */
    public $authentication;

    //
    const STAGING_BASE_URL = 'https://staging.authservices.satispay.com';
    const PRODUCTION_BASE_URL = 'https://authservices.satispay.com';

    //
    const VERSION = '2.0.0';
    const USER_AGENT = 'SatispayGBusinessApiPhpSdk';

    /**
     * SatispayClient constructor.
     *
     * @param array<string, mixed> $config Configuration options for the client.
     */
    public function __construct($config = [])
    {
        $this->config = array_merge(
            self::DEFAULT_CONFIG,
            [
                'headers' => [
                    'User-Agent' => self::USER_AGENT . '/' . self::VERSION . ' PHP/' . phpversion()
                ]
            ],
            $config
        );

        $this->http = new HTTPService(
            $this,
            $this->config['sandbox'] === true ? self::STAGING_BASE_URL : self::PRODUCTION_BASE_URL,
            $this->config['psr']
        );

        $this->authentication = new AuthenticationService(
            $this,
            $this->config['public_key'],
            $this->config['private_key'],
            $this->config['key_id'],
            $this->config['passphrase']
        );

        $this->boot();
    }

    /**
     * Function that instantiate all the dependencies.
     *
     * @return void
     */
    protected function boot() {}

    /**
     * Get the current configuration settings for the SatispayClient.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the default SatispayClient headers.
     *
     * @return array
     */
    public function getHeaders()
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
    public function sandbox()
    {
        return $this->config['sandbox'] === true;
    }
}
