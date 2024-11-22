<?php

namespace EmanueleCoppola\Satispay\Exceptions;

use EmanueleCoppola\Satispay\SatispayResponse;

/**
 * Class SatispayResponseException
 *
 * The class representing an exception related to Satispay responses.
 */
class SatispayResponseException extends SatispayException {

    /**
     * The Satispay response where the exception has been thrown.
     *
     * @var SatispayResponse
     */
    protected SatispayResponse $response;

    /**
     * BaseResponseException constructor.
     *
     * @param SatispayResponse $satispayResponse The SatispayResponse object representing the API response.
     * @param string|null The message shown in the exception.
     */
    public function __construct(SatispayResponse $satispayResponse, string|null $message = null)
    {
        $this->response = $satispayResponse;
        $this->code = $satispayResponse->getErrorCode();

        $this->message = [];

        if (is_array($message)) {
            $this->message = array_merge($this->message, $message);
        } else if (is_string($message)) {
            $this->message[] = $message;
        }

        $this->message[] = "";
        $this->message[] = "cid: {$this->response->getCID()}";
        $this->message[] = "code: {$this->code}";
        $this->message[] = "env: {$this->response->getEnv()}";
        $this->message[] = "";

        $this->message = implode("\n", $this->message);
    }

    /**
     * Get the CID (Correlation ID) from the Satispay response.
     *
     * @return string
     */
    public function getCID(): string
    {
        return $this->response->getCID();
    }

    /**
     * Get the request environment.
     *
     * @return string
     */
    public function getEnv(): string
    {
        return $this->response->getCID();
    }
}
