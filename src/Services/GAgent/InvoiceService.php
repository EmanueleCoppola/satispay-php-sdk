<?php

namespace EmanueleCoppola\Satispay\Services\GAgent;

use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class InvoiceService
 *
 * Service class for managing Connect payment invoices using the Satispay GAgent API.
 */
class InvoiceService extends BaseService {

    /**
     * Get an invoice.
     *
     * @see https://connect.satispay.com/reference/retrieve-a-pagopa-invoice-by-id
     *
     * @param string $id The id of the invoice to retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function get(string $id, array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_agent/v1/pagopa/invoices/' . $id,
            [],
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Retrieve a list of all invoices.
     *
     * @see https://connect.satispay.com/reference/retrieve-all-invoices
     *
     * @param array $query Additional query parameters for filtering the invoices.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function all(array $query = [], array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_agent/v1/pagopa/invoices',
            $query,
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }
}
