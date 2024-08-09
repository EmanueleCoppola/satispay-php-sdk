<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class ReportService
 *
 * Service class for managing MQTT using the Satispay GBusiness API.
 */
class MQTTService extends BaseService {

    /**
     * The host used for the MQTT connection.
     *
     * @var string|null
     */
    public $host;

    /**
     * The port used for the MQTT connection.
     *
     * @var string|null
     */
    public $port;

    /**
     * The PEM certificate to be used with MQTT connections.
     *
     * @var string|null
     */
    public $certificatePem;

    /**
     * The private key to be used with MQTT connections.
     *
     * @var string|null
     */
    public $privateKey;

    /**
     * The shop uid required for MQTT subscription.
     *
     * @var string|null
     */
    public $shopUid;

    /**
     * MQTTService constructor.
     *
     * @inheritdoc
     * @param string|null $host The host used for the MQTT connection.
     * @param string|null $port The port used for the MQTT connection.
     * @param string|null $certificatePem The PEM certificate to be used with MQTT connections.
     * @param string|null $privateKey The private key to be used with MQTT connections.
     * @param string|null $shopUid The shop uid required for MQTT subscription.
     */
    public function __construct($context, $host = null, $port = 8883, $certificatePem = null, $privateKey = null, $shopUid = null)
    {
        parent::__construct($context);

        $this->host = $host;
        $this->port = $port;

        $this->certificatePem = $certificatePem;
        $this->privateKey = $privateKey;

        $this->shopUid = $shopUid;
    }

    /**
     * Check if the MQTT authentication parameters are set.
     *
     * This method verifies whether the necessary authentication parameters such as PEM certificate,
     * private key, and shop uid are set, indicating a possible successful authentication.
     * Returns true if all parameters are set, false otherwise.
     *
     * @return bool
     */
    public function ready()
    {
        return !(
            (empty($this->host) && empty($this->port)) ||
            empty($this->certificatePem) || empty($this->privateKey) || empty($this->shopUid)
        );
    }

    /**
     * Create a new MQTT certificate.
     *
     * @see https://developers.satispay.com/reference/create-mqtt-certificates
     *
     * @param array $payload The payload data for creating an MQTT certificate.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return $this
     */
    public function authenticate($headers = []) {
        $response = $this->context->http->post(
            '/g_business/v1/mqtt_certificates',
            [],
            true,
            $headers
        );

        $response->checkExceptions();

        $response = $response->toArray();

        if (
            key_exists('certificate_pem', $response) &&
            key_exists('private_key', $response) &&
            key_exists('shop_uid', $response)
        ) {
            $this->certificatePem = $response['certificate_pem'];
            $this->privateKey = $response['private_key'];
            $this->shopUid = $response['shop_uid'];
        }

        return $this;
    }

    /**
     * Generate an MQTT client id with a random seed.
     *
     * @return string
     */
    public function clientId()
    {
        $clientId = [];

        $clientId[] = $this->context->sandbox() ? 'staging/shop' : 'shop';
        $clientId[] = $this->shopUid;
        $clientId[] = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);

        return implode('_', $clientId);
    }
}
