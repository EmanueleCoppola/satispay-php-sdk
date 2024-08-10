<?php

namespace EmanueleCoppola\Satispay;

use EmanueleCoppola\Satispay\Services\GAgent\PaymentService;
use EmanueleCoppola\Satispay\Services\GAgent\ReceiptService;

/**
 * Class SatispayGAgentClient
 *
 * A client for interacting with the Satispay g_agent APIs, providing an abstraction for all the functionality.
 */
class SatispayGAgentClient extends SatispayClient {

    /**
     * The service that handles payments APIs.
     *
     * @var PaymentService
     */
    public $payments;

    /**
     * The service that handles receipts APIs.
     *
     * @var ReceiptService
     */
    public $receipts;

    /**
     * @inheritdoc
     */
    protected function boot($config)
    {
        $this->payments = new PaymentService($this);

        $this->receipts = new ReceiptService($this);
    }
}
