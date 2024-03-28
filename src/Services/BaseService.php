<?php

namespace Satispay\Services;

use Satispay\SatispayClient;

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
     * @param mixed|null $context The context parameter, typically an instance of SatispayClient.
     */
    public function __construct($context = null)
    {
        $this->context = $context;
    }
}
