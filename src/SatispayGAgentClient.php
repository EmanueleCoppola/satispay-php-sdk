<?php

namespace Satispay;

use Satispay\Services\GAgent\PaymentService;

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
