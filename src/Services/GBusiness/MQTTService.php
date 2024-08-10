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
     * The client certificate to be used with MQTT connections.
     *
     * @var string|null
     */
    public $clientCertificate;

    /**
     * The client certificate key to be used with MQTT connections.
     *
     * @var string|null
     */
    public $clientCertificateKey;

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
     * @param string|null $clientCertificate The client certificate to be used with MQTT connections.
     * @param string|null $clientCertificateKey The client certificate key to be used with MQTT connections.
     * @param string|null $shopUid The shop uid required for MQTT subscription.
     */
    public function __construct($context, $host = null, $port = 8883, $clientCertificate = null, $clientCertificateKey = null, $shopUid = null)
    {
        parent::__construct($context);

        $this->host = $host;
        $this->port = $port;

        $this->clientCertificate = $clientCertificate;
        $this->clientCertificateKey = $clientCertificateKey;

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
            empty($this->clientCertificate) || empty($this->clientCertificateKey) || empty($this->shopUid)
        );
    }

    /**
     * Ensure that MQTT authentication parameters are set.
     *
     * This method checks whether the MQTT authentication parameters are correctly set.
     * If the parameters are not set, it throws a `SatispayException` indicating that authentication is required
     * before proceeding with MQTT operations.
     *
     * @throws SatispayException if authentication parameters are not set.
     * @return void
     */
    protected function ensureReady()
    {
        if (!$this->ready()) {
            throw new SatispayException('Please authenticate to MQTT first!');
        }
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
            $this->clientCertificate = $response['certificate_pem'];
            $this->clientCertificateKey = $response['private_key'];
            $this->shopUid = $response['shop_uid'];
        }

        return $this;
    }

    /**
     * Generate an MQTT client id with a random seed.
     *
     * @throws SatispayException
     * @return string
     */
    public function clientId()
    {
        $this->ensureReady();

        $clientId = [];

        $clientId[] = $this->context->sandbox() ? 'staging/shop' : 'shop';
        $clientId[] = $this->shopUid;
        $clientId[] = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);

        return implode('_', $clientId);
    }

    /**
     * The MQTT topic for subscribing to the fund lock notifications.
     *
     * @throws SatispayException
     * @return string
     */
    public function fundLockTopic()
    {
        $this->ensureReady();

        $topic = [];

        $topic[] = $this->context->sandbox() ? 'staging/' : '';
        $topic[] = 'transaction/v1/fund_lock/owner/+/recipient/';
        $topic[] = $this->shopUid;

        return implode('', $topic);
    }
}
