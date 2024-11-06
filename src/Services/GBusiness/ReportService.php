<?php

namespace EmanueleCoppola\Satispay\Services\GBusiness;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\Exceptions\SatispayResponseException;
use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class ReportService
 *
 * Service class for managing reports using the Satispay GBusiness API.
 */
class ReportService extends BaseService {

    /**
     * Create a new report.
     *
     * @see https://developers.satispay.com/reference/create-new-report
     *
     * @param array $body The body for creating a report.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function create($body, $headers = []) {
        $response = $this->context->http->post(
            '/g_business/v1/reports',
            $body,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Retrieve details of a specific report.
     *
     * @see https://developers.satispay.com/reference/retrieve-a-report
     *
     * @param string $id The ID of the report to retrieve.
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
            '/g_business/v1/reports/' . $id,
            [],
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }

    /**
     * Retrieve a list of all reports.
     *
     * @see https://developers.satispay.com/reference/get-list-of-reports
     *
     * @param array $query Additional query parameters for filtering reports.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function all($query = [], $headers = [])
    {
        $response = $this->context->http->get(
            '/g_business/v1/reports',
            $query,
            true,
            $headers
        );

        $response->checkExceptions();

        return $response;
    }
}
