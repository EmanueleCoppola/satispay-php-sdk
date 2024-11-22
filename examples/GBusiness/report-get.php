<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayGBusinessClient;

if (!file_exists('_report-authentication.json')) die('_report-authentication.json file not available!');

$authentication = json_decode(file_get_contents('_report-authentication.json'), true);

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

$report = $satispayGBusinessClient->reports->get('6f90eab2-c900-4e2e-9735-cb306ddf2263');

echo $report->toJson();
