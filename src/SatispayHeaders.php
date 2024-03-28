<?php

namespace Satispay;

/**
 * Class SatispayHeaders
 *
 * Constants representing various headers used in Satispay API requests.
 * We are not using modern enums because of backward compatibility.
 */
class SatispayHeaders {
    const IDEMPOTENCY_KEY = 'Idempotency-Key';

    // x-
    const OS         = 'x-satispay-os';
    const OS_VERSION = 'x-satispay-osv';

    const APP_SOFTWARE_HOUSE = 'x-satispay-apph';
    const APP_VERSION        = 'x-satispay-appv';
    const APP_NAME           = 'x-satispay-appn';

    const DEVICE_TYPE = 'x-satispay-devicetype';

    const TRACKING_CODE = 'x-satispay-tracking-code';
}
