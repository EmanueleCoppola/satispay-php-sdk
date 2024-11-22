<?php

namespace EmanueleCoppola\Satispay\Services\GAgent;

use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class ReceiptService
 *
 * Service class for managing Connect payment receipts using the Satispay GAgent API.
 */
class ReceiptService extends BaseService {

    /**
     * Get a receipt.
     *
     * @see https://connect.satispay.com/reference/get-receipt
     *
     * @param string $id The id of the payment for the receipt to retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function get(string $id, array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_agent/v1/pagopa/payments/' . $id . '/receipt',
            [],
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }
}
