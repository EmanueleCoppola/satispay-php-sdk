<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class SessionService
 *
 * Service class for managing sessions using the Satispay GBusiness API.
 */
class SessionService extends BaseService {

    /**
     * Create a new payment session.
     *
     * @see https://developers.satispay.com/reference/open-session
     *
     * @param array $body The body for creating a payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function create($body, $headers = []) {
        $response = $this->context->http->post(
            '/g_business/v1/sessions',
            $body,
            true,
            $headers
        );

        $response->checkExceptions([
            400 => [
                135 => "Invalid fund lock for a new session."
            ]
        ]);

        return $response;
    }

    /**
     * Retrieve details of a specific session.
     *
     * @see https://developers.satispay.com/reference/get-session-details
     *
     * @param string $id The ID of the session to retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function get($id, $headers = [])
    {
        $response = $this->context->http->get(
            '/g_business/v1/sessions/' . $id,
            [],
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Update a specific session.
     *
     * @see https://developers.satispay.com/reference/update-session
     *
     * @param string $id The ID of the session to update.
     * @param array $body The updated data for the payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function update($id, $body = [], $headers = [])
    {
        $response = $this->context->http->patch(
            '/g_business/v1/sessions/' . $id,
            $body,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Create a new event for a payment session.
     *
     * @see https://developers.satispay.com/reference/create-session-event
     *
     * @param array $id The ID of the session to create events in.
     * @param array $body The body for creating a payment.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function createEvent($id, $body, $headers = []) {
        $response = $this->context->http->post(
            '/g_business/v1/sessions/' . $id . '/events',
            $body,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }
}
