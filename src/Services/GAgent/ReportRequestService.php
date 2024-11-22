<?php

namespace EmanueleCoppola\Satispay\Services\GAgent;

use EmanueleCoppola\Satispay\SatispayResponse;
use EmanueleCoppola\Satispay\Services\BaseService;

/**
 * Class ReportRequestService
 *
 * Service class for managing Connect report requests using the Satispay GAgent API.
 */
class ReportRequestService extends BaseService {

    /**
     * Create a new report request.
     *
     * @see https://connect.satispay.com/reference/a
     *
     * @param array $body The body for creating a report request.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @throws SatispayException
     * @throws SatispayResponseException
     *
     * @return SatispayResponse
     */
    public function create($body, $headers = []) {
        $response = $this->context->http->post(
            '/g_agent/v1/pagopa/report_requests',
            $body,
            true,
            $headers
        );

        return $response;
    }

    /**
     * Get a report request.
     *
     * @see https://connect.satispay.com/reference/create-a-new-pagopa-report-request-copy
     *
     * @param string $id The id of the report request retrieve.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function get($id, $headers = [])
    {
        return $this->context->http->get(
            '/g_agent/v1/pagopa/report_requests/' . $id,
            [],
            true,
            $headers
        );
    }

    /**
     * Retrieve a list of all report requests.
     *
     * @see https://connect.satispay.com/reference/retrieve-a-pagopa-report-request-by-id-copy
     *
     * @param array $query Additional query parameters for filtering report requests.
     * @param array $headers Additional headers for the HTTP request.
     *
     * @return SatispayResponse
     */
    public function all($query = [], $headers = [])
    {
        return $this->context->http->get(
            '/g_agent/v1/pagopa/report_requests',
            $query,
            true,
            $headers
        );
    }
}
