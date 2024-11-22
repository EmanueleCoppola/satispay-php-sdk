<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
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
     * @see https://developers.satispay.com/reference/create-a-payment
     *
     * @param array $body The body for creating a payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function create(array $body, array $headers = []): SatispayResponse
    {
        $response = $this->context->http->post(
            '/g_business/v1/payments',
            $body,
            $headers,
            true
        );

        $response->checkExceptions([
            400 => [
                21 => "The wallet that you're charging has insufficient funds.",
                27 => [
                    "Payment not allowed.",
                    "The shop or the user are not able to pay.",
                    "This condition could be temporary."
                ],
                36 => [
                    "Malformed flow, payment or metadata.",
                    "Please check your input."
                ],
                172 => "The pre-authorization token is not valid.",
                131 => [
                    "This payment can't be refunded.",
                    "Payments can't be refunded within 365 days from creation."
                ]
            ]
        ]);

        return $response;
    }

    /**
     * Retrieve details of a specific payment.
     *
     * @see https://developers.satispay.com/reference/get-the-details-of-a-payment
     *
     * @param string $id The ID of the payment to retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function get(string $id, array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_business/v1/payments/' . $id,
            [],
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Update a specific payment.
     *
     * @see https://developers.satispay.com/reference/update-a-payment
     *
     * @param string $id The ID of the payment to update.
     * @param array $body The updated data for the payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function update(string $id, array $body = [], array $headers = []): SatispayResponse
    {
        $response = $this->context->http->put(
            '/g_business/v1/payments/' . $id,
            $body,
            $headers,
            true
        );

        $response->checkExceptions([
            400 => [
                36 => [
                    "Invalid parameters.",
                    "Please check your input."
                ]
            ],
            401 => [
                34 => [
                    "Shop not found or unauthorized.",
                    "Please generate a new authentication code and try again."
                ]
            ],
            403 => [
                44 => [
                    "Illegal state transition.",
                    "The action that you are trying to perform can't be executed on this specific payment."
                ],
                45 => "Unable to fulfill the request.",
                70 => [
                    "Anti-hammering violation",
                    "Please try again in a while."
                ]
            ],
        ]);

        return $response;
    }

    /**
     * Retrieve a list of all payments.
     *
     * @see https://developers.satispay.com/reference/get-list-of-payments
     *
     * @param array $query Additional query parameters for filtering payments.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function all(array $query = [], array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_business/v1/payments',
            $query,
            $headers,
            true
        );

        $response->checkExceptions([
            400 => [
                36 => [
                    "Invalid query parameters.",
                    "Please check your input."
                ]
            ],
            401 => [
                34 => [
                    "Shop not found or unauthorized.",
                    "Please generate a new authentication code and try again."
                ]
            ],
            403 => [
                45 => "Unable to fulfill the request.",
            ]
        ]);

        return $response;
    }
}
