<?php

namespace EmanueleCoppola\Satispay;

use EmanueleCoppola\Satispay\Services\GAgent\InvoiceService;
use EmanueleCoppola\Satispay\Services\GAgent\PaymentService;
use EmanueleCoppola\Satispay\Services\GAgent\ReceiptService;
use EmanueleCoppola\Satispay\Services\GAgent\ReportRequestService;

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
     * The service that handles invoices APIs.
     *
     * @var InvoiceService
     */
    public $invoices;

    /**
     * The service that handles receipts APIs.
     *
     * @var ReceiptService
     */
    public $receipts;

    /**
     * The service that handles report requests APIs.
     *
     * @var ReportRequestService
     */
    public $reportRequests;

    /**
     * @inheritdoc
     */
    protected function boot($config)
    {
        $this->payments = new PaymentService($this);

        $this->invoices = new InvoiceService($this);

        $this->receipts = new ReceiptService($this);

        $this->reportRequests = new ReportRequestService($this);
    }
}
