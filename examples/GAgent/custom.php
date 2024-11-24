<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayClient;
use EmanueleCoppola\Satispay\SatispayGAgentClient;
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

if (!file_exists('_authentication.json')) die('_authentication.json file not available!');

$authentication = json_decode(file_get_contents('_authentication.json'), true);

$satispayGAgentClient = new SatispayGAgentClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

$paymentNoticeNumber = $satispayGAgentClient->payments->randomPaymentNoticeNumber(); // just for testing

// This has been created in case a new API comes out
// and you want to test it before this SDK gets a new release
// the same applies to the SatispayGAgentClient and SatispayGBusinessClient instances
$payment = $satispayGAgentClient->post(
    'g_agent/v1/pagopa/payments',
    [
        'request_type' => 'MANUAL',
        'payment_notice_number' => $paymentNoticeNumber,
        'domain_id' => '00000000000',
        'amount_unit' => 150.99 * 100, // 150,99€
    ]
);

echo $payment->toJson();
