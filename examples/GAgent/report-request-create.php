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

$pagoPaReportRequest = $satispayGAgentClient->reportRequests->create(
    [
        'from' => '2024-11-01',
        'to' => '2024-11-30',
        'time_zone' => 'Europe/Rome',
        'format' => 'CSV'
    ],
    [
        // SatispayHeaders::IDEMPOTENCY_KEY => 'myidempotency'
    ]
);

echo $pagoPaReportRequest->toJson();