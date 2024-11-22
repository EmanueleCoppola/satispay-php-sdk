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
    public function get($id, $headers = [])
    {
        return $this->context->http->get(
            '/g_agent/v1/pagopa/invoices/' . $id,
            [],
            true,
            $headers
        );
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
    public function all($query = [], $headers = [])
    {
        return $this->context->http->get(
            '/g_agent/v1/pagopa/invoices',
            $query,
            true,
            $headers
        );
    }
}