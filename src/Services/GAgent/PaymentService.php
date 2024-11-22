<?php

namespace EmanueleCoppola\Satispay\Services\GAgent;

use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class PaymentService
 *
 * Service class for managing Connect payments using the Satispay GBusiness API.
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
        $response = $this->context->http->post(
            '/g_agent/v1/pagopa/payments',
            $payload,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Retrieve details of a specific payment.
     *
     * @see https://connect.satispay.com/reference/create-a-new-pagopa-payment
     *
     * @param string $id The id of the payment to retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function get($id, $headers = [])
    {
        $response = $this->context->http->get(
            '/g_agent/v1/pagopa/payments/' . $id,
            [],
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
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
        $response = $this->context->http->patch(
            '/g_agent/v1/pagopa/payments/' . $id,
            $body,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
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
        $response = $this->context->http->get(
            '/g_agent/v1/pagopa/payments',
            $query,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }
}
