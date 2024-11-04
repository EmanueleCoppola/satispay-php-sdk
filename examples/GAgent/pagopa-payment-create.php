<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayGAgentClient;

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

$noticeNumber = str_pad(mt_rand(0, pow(10, 18) - 1), 18, '0', STR_PAD_LEFT); // random

$pagoPaPayment = $satispayGAgentClient->payments->create(
    [
        'request_type' => 'MANUAL',
        'payment_notice_number' => $noticeNumber,
        'domain_id' => '00000000000',
        'amount_unit' => 150.99 * 100, // 150,99€
    ],
    [
        // SatispayHeaders::IDEMPOTENCY_KEY => 'myidempotency'
    ]
);

echo $pagoPaPayment->toJson();
