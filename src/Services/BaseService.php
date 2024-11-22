<?php

namespace EmanueleCoppola\Satispay\Services;

use EmanueleCoppola\Satispay\SatispayClient;

abstract class BaseService {

    /**
     * The SatispayClient instance associated with this service context.
     *
     * @var SatispayClient
     */
    protected SatispayClient $context;

    /**
     * BaseService constructor.
     *
     * Initializes the BaseService with an optional context parameter.
     *
     * @param SatispayClient $context The context parameter, typically an instance of SatispayClient.
     */
    public function __construct(SatispayClient $context)
    {
        $this->context = $context;
    }
}
