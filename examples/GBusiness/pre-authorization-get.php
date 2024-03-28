<?php

require_once('../../vendor/autoload.php');

use Satispay\SatispayGBusinessClient;

if (!file_exists('_authentication.json')) die('_authentication.json file not available!');

$authentication = json_decode(file_get_contents('_authentication.json'), true);

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    'sandbox' => true
]);

$preAuthorization = $satispayGBusinessClient->preAuthorizations->get('2946662c-7c4e-4ad7-8b18-4c5d5c844b14');

echo $preAuthorization->toJson();
