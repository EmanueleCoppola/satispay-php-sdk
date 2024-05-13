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

$response = $satispayGBusinessClient->authentication->test();
$res = $response->toArray();

if (in_array($res['authentication_key']['role'], ['ONLINE_SHOP', 'DEVICE'])) {
    echo 'Authentication successfull! ' . $response->getCid();
} else {
    echo 'Authentication not successfull!';
}
