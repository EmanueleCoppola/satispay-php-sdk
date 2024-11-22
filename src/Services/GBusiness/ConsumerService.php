<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class ConsumerService
 *
 * Service class for retrieving consumers using the Satispay GBusiness API.
 */
class ConsumerService extends BaseService {

    /**
     * Retrieve a consumer by the phone number.
     *
     * @see https://developers.satispay.com/reference/retrive-consumer
     *
     * @param string $number The phone number of the consumer.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function get(string $number, array $headers = []): SatispayResponse
    {
        $response = $this->context->http->get(
            '/g_business/v1/consumers/' . $number,
            [],
            $headers,
            true
        );

        $response->checkExceptions();

        return $response;
    }
}
