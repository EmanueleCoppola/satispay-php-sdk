<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class ProfileService
 *
 * Service class for retrieving the merchant profile using the Satispay GBusiness API.
 */
class ProfileService extends BaseService {

    /**
     * Retrieve the shop profile.
     *
     * @see https://developers.satispay.com/reference/retrieve-shop-profile
     *
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function me(array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_business/v1/profile/me',
            [],
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }
}
