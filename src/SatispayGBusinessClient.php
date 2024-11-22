<?php

namespace EmanueleCoppola\Satispay;

use EmanueleCoppola\Satispay\Services\GBusiness\ProfileService;
use EmanueleCoppola\Satispay\Services\GBusiness\ConsumerService;
use EmanueleCoppola\Satispay\Services\GBusiness\DailyClosureService;
use EmanueleCoppola\Satispay\Services\GBusiness\MQTTService;
use EmanueleCoppola\Satispay\Services\GBusiness\PaymentService;
use EmanueleCoppola\Satispay\Services\GBusiness\PreAuthorizationService;
use EmanueleCoppola\Satispay\Services\GBusiness\ReportService;
use EmanueleCoppola\Satispay\Services\GBusiness\SessionService;

/**
 * Class SatispayGBusinessClient
 *
 * A client for interacting with the Satispay g_business APIs, providing an abstraction for all the functionality.
 */
class SatispayGBusinessClient extends SatispayClient {

    //
    const STAGING_MQTT_SERVER = 'a3nj958dbfb5ge.iot.eu-west-1.amazonaws.com';
    const PRODUCTION_MQTT_SERVER = 'a186ick0qcrau4-ats.iot.eu-west-1.amazonaws.com';

    //
    const STAGING_MQTT_CERTIFICATE = 'https://cacerts.digicert.com/pca3-g5.crt.pem';
    const PRODUCTION_MQTT_CERTIFICATE = 'https://www.amazontrust.com/repository/AmazonRootCA1.pem';

    //
    const MQTT_PORT = 8883;

    /**
     * The service that handles /profile APIs.
     *
     * @var ProfileService
     */
    public ProfileService $profile;

    /**
     * The service that handles /consumers APIs.
     *
     * @var ConsumerService
     */
    public ConsumerService $consumers;

    /**
     * The service that handles /payments APIs.
     *
     * @var PaymentService
     */
    public PaymentService $payments;

    /**
     * The service that handles /pre_authorized_payment_tokens APIs.
     *
     * @var PreAuthorizationService
     */
    public PreAuthorizationService $preAuthorizations;

    /**
     * The service that handles /reports APIs.
     *
     * @var ReportService
     */
    public ReportService $reports;

    /**
     * The service that handles /daily_closure APIs.
     *
     * @var DailyClosureService
     */
    public DailyClosureService $dailyClosures;

    /**
     * The service that handles the MQTT connection.
     *
     * @var MQTTService
     */
    public MQTTService $mqtt;

    /**
     * The service that handles the /sessions APIs.
     *
     * @var SessionService
     */
    public SessionService $sessions;

    /**
     * @inheritdoc
     */
    protected function boot(array $config): void
    {
        $this->profile = new ProfileService($this);

        $this->consumers = new ConsumerService($this);

        $this->payments = new PaymentService($this);

        $this->preAuthorizations = new PreAuthorizationService($this);

        $this->reports = new ReportService($this);

        $this->dailyClosures = new DailyClosureService($this);

        $this->mqtt = new MQTTService(
            $this,
            $this->sandbox() ? self::STAGING_MQTT_SERVER : self::PRODUCTION_MQTT_SERVER,
            self::MQTT_PORT,
            $config['mqtt_client_certificate'],
            $config['mqtt_client_certificate_key'],
            $config['mqtt_shop_uid']
        );

        $this->sessions = new SessionService($this);
    }
}
