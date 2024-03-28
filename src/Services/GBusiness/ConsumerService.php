<?php

namespace Satispay\Services\GBusiness;

use Satispay\Exceptions\SatispayException;
use Satispay\Exceptions\SatispayResponseException;
use Satispay\SatispayResponse;
use Satispay\Services\BaseService;

class ConsumerService extends BaseService {

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
    public function get($number, $headers = [])
    {
        $response = $this->context->http->get(
            '/g_business/v1/consumers/' . $number,
            [],
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }
}
