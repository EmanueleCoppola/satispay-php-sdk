<?php

require_once('../../vendor/autoload.php');

use Satispay\SatispayGBusinessClient;

if (!file_exists('_authentication.json')) die('_authentication.json file not available!');

$authentication = json_decode(file_get_contents('_authentication.json'), true);

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

$report = $satispayGBusinessClient->reports->create([
    'type' => 'PAYMENT_FEE',
    'format' => 'CSV',
    'query_type' => 'PAYMENT_DATE_INTERVAL',
    'query_payload' => [
        'payment_date_from' => '2024-01-01',
        'payment_date_to' => '2024-01-02',
        'time_zone' => 'Europe/Rome'
    ]
]);

echo $report->toJson();
