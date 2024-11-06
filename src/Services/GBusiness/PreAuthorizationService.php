<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class PreAuthorizationService
 *
 * Service class for managing pre-authorizations using the Satispay GBusiness API.
 */
class PreAuthorizationService extends BaseService {

    /**
     * Create a new pre-authorization.
     *
     * @see https://developers.satispay.com/reference/create-authorization
     *
     * @param array $body The body for creating a pre-authorization.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function create($body, $headers = [])
    {
        $response = $this->context->http->post(
            '/g_business/v1/pre_authorized_payment_tokens',
            $body,
            true,
            $headers
        );

        $response->checkExceptions([
            400 => [
                36 => "Missing or invalid fields."
            ]
        ]);

        return $response;
    }

    /**
     * Retrieve details of a specific pre-authorization.
     *
     * @see https://developers.satispay.com/reference/get-authorization
     *
     * @param string $id The ID of the pre-authorization to retrieve.
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
            '/g_business/v1/pre_authorized_payment_tokens/' . $id,
            [],
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Update details of a specific pre-authorization.
     *
     * @see https://developers.satispay.com/reference/update-authorization
     *
     * @param string $id The ID of the pre-authorization to update.
     * @param array $body The updated data for the pre-authorization.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function update($id, $body = [], $headers = [])
    {
        $response = $this->context->http->put(
            '/g_business/v1/pre_authorized_payment_tokens/' . $id,
            $body,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }
}
