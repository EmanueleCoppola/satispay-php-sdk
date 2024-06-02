<?php

namespace EmanueleCoppola\Satispay;

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

    /**
     * RSA service instance for managing RSA keys.
     *
     * @var RSAServiceContract
     */
    public $rsa_service;

    //
    const STAGING_BASE_URL = 'https://staging.authservices.satispay.com';
    const PRODUCTION_BASE_URL = 'https://authservices.satispay.com';

    //
    const VERSION = '1.0.0';
    const USER_AGENT = 'EmanueleCoppola/satispay-php-sdk';

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

        $this->rsa_service = $this->resolveRSAService(
            $this->config['rsa_service'],
            $this->config['public_key'],
            $this->config['private_key'],
            $this->config['passphrase']
        );

        $this->authentication = new AuthenticationService($this, $this->config['key_id']);

        $this->http = new HTTPService(
            $this,
            $this->config['sandbox'] === true ? self::STAGING_BASE_URL : self::PRODUCTION_BASE_URL,
            $this->config['psr']
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
     * Resolve the RSA service to be used.
     * OpenSSL will be used if the OpenSSL extension is available.
     * The phpseclib will be used if found in the composer.json.
     *
     * @throws SatispayRSAException if non of the services is available.
     *
     * @return RSAServiceContract|void
     */
    private function resolveRSAService(
        $RSAServices,
        $publicKey = null,
        $privateKey = null,
        $passphrase = null
    )
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
