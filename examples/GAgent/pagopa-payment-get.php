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

$pagoPaPayment = $satispayGAgentClient->payments->get('1f4b5397-f60a-4a98-81f0-6f3aef88bb80');

echo $pagoPaPayment->toJson();
