<?php

namespace EmanueleCoppola\Satispay;

use EmanueleCoppola\Satispay\Services\GAgent\PaymentService;

/**
 * Class SatispayGAgentClient
 *
 * A client for interacting with the Satispay g_agent APIs, providing an abstraction for all the functionality.
 */
class SatispayGAgentClient extends SatispayClient {

    /**
     * The service that handles /payments APIs.
     *
     * @var PaymentService
     */
    public $payments;

    /**
     * @inheritdoc
     */
    protected function boot()
    {
        $this->payments = new PaymentService($this);
    }
}
