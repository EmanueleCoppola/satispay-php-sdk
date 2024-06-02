<?php

namespace EmanueleCoppola\Satispay\Services\GAgent;

use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class PaymentService
 *
 * Service class for managing payments using the Satispay GBusiness API.
 */
class PaymentService extends BaseService {

    /**
     * Create a new payment.
     *
     * @see https://connect.satispay.com/reference/create-a-new-pagopa-payment
     *
     * @param array $payload The payload data for creating a payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function create($payload, $headers = []) {
        return $this->context->http->post(
            '/g_agent/v1/pagopa/payments',
            $payload,
            true,
            $headers
        );
    }

    /**
     * Retrieve details of a specific payment.
     *
     * @see https://connect.satispay.com/reference/create-a-new-pagopa-payment
     *
     * @param string $id The ID of the payment to retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function get($id, $headers = [])
    {
        return $this->context->http->get(
            '/g_agent/v1/pagopa/payments/' . $id,
            [],
            true,
            $headers
        );
    }

    /**
     * Update details of a specific payment.
     *
     * @see https://connect.satispay.com/reference/update-a-pagopa-payment
     *
     * @param string $id The ID of the payment to update.
     * @param array $body The updated data for the payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function update($id, $body = [], $headers = [])
    {
        return $this->context->http->put(
            '/g_agent/v1/pagopa/payments/' . $id,
            $body,
            true,
            $headers
        );
    }

    /**
     * Retrieve a list of all payments.
     *
     * @see https://connect.satispay.com/reference/retrieve-all-payments
     *
     * @param array $query Additional query parameters for filtering payments.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function all($query = [], $headers = [])
    {
        return $this->context->http->get(
            '/g_agent/v1/pagopa/payments',
            $query,
            true,
            $headers
        );
    }
}
