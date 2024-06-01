<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class DailyClosureService
 *
 * Service class for retrieving daily closures using the Satispay GBusiness API.
 */
class DailyClosureService extends BaseService {

    /**
     * Retrieve a specific daily closure.
     *
     * @see https://developers.satispay.com/reference/get-the-details-of-a-payment
     *
     * @param string $date The day on which retrieve the daily closure.
     * @param array $query Additional query parameters.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function get($date, $query = [], $headers = [])
    {
        $response = $this->context->http->get(
            '/g_business/v1/daily_closure/' . $date,
            $query,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }
}
