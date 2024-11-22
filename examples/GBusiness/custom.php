<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayClient;
use EmanueleCoppola\Satispay\SatispayGAgentClient;
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

if (!file_exists('_authentication.json')) die('_authentication.json file not available!');

$authentication = json_decode(file_get_contents('_authentication.json'), true);

$satispayClient = new SatispayClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

// This has been created in case a new API comes out
// and you want to test it before this SDK gets a new release
// the same applies to the SatispayGAgentClient and SatispayGBusinessClient instances
$payment = $satispayClient->post(
    'g_business/v1/payments',
    [
        'flow' => 'MATCH_CODE',
        'currency' => 'EUR',
        'amount_unit' => 12.99 * 100, // 12,99€
    ]
);

echo $payment->toJson();
