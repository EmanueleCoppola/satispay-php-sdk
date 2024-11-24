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
            $headers,
            true
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
            $headers,
            true
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
    public function update(string $id, array $body = [], array $headers = []): SatispayResponse
    {
        $response = $this->context->http->patch(
            '/g_agent/v1/pagopa/payments/' . $id,
            $body,
            $headers,
            true
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
    public function all(array $query = [], array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_agent/v1/pagopa/payments',
            $query,
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Generates a random payment notice number.
     *
     * @return string The generated payment notice number string.
     */
    public function randomPaymentNoticeNumber() {
        $length = 18;
        $prefix = '1';

        // these controls are intentionally left for potential future parametrization
        if ($prefix) {
            $length -= strlen($prefix);
        }

        $length = max($length, 0);

        return $prefix . str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
