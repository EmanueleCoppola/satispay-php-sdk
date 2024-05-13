<?php

require_once('../../vendor/autoload.php');

use Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([
    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

$satispayGBusinessClient->authentication->authenticate('QHPYXD');

$publicKey = $satispayGBusinessClient->authentication->publicKey;
$privateKey = $satispayGBusinessClient->authentication->privateKey;
$keyId = $satispayGBusinessClient->authentication->keyId;

if ($satispayGBusinessClient->authentication->ready()) {
    file_put_contents(
        '_authentication.json',
        json_encode([
            'public_key' => $publicKey,
            'private_key' => $privateKey,
            'key_id' => $keyId
        ], JSON_PRETTY_PRINT)
    );
}
