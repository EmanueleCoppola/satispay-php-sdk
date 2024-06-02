<?php

namespace EmanueleCoppola\Satispay;

use EmanueleCoppola\Satispay\Services\GBusiness\ConsumerService;
use EmanueleCoppola\Satispay\Services\GBusiness\DailyClosureService;
use EmanueleCoppola\Satispay\Services\GBusiness\PaymentService;
use EmanueleCoppola\Satispay\Services\GBusiness\PreAuthorizationService;
use EmanueleCoppola\Satispay\Services\GBusiness\ReportService;

/**
 * Class SatispayGBusinessClient
 *
 * A client for interacting with the Satispay g_business APIs, providing an abstraction for all the functionality.
 */
class SatispayGBusinessClient extends SatispayClient {

    /**
     * The service that handles /payments APIs.
     *
     * @var PaymentService
     */
    public $payments;

    /**
     * The service that handles /pre_authorized_payment_tokens APIs.
     *
     * @var PreAuthorizationService
     */
    public $preAuthorizations;

    /**
     * The service that handles /reports APIs.
     *
     * @var ReportService
     */
    public $reports;

    /**
     * The service that handles /consumers APIs.
     *
     * @var ConsumerService
     */
    public $consumers;

    /**
     * The service that handles /daily_closure APIs.
     *
     * @var DailyClosureService
     */
    public $dailyClosures;

    /**
     * @inheritdoc
     */
    protected function boot()
    {
        $this->payments = new PaymentService($this);

        $this->preAuthorizations = new PreAuthorizationService($this);

        $this->reports = new ReportService($this);

        $this->consumers = new ConsumerService($this);

        $this->dailyClosures = new DailyClosureService($this);
    }
}
