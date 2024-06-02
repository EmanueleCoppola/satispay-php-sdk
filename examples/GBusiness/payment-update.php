<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayGBusinessClient;

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

$update = $satispayGBusinessClient->payments->update(
    '7b5g32m5-3166-4c01-4617-edrb41558ce7',
    [
        'action' => 'ACCEPT',
    ]
);

echo $update->toJson();
