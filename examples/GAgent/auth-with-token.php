<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayGAgentClient;

$satispayGAgentClient = new SatispayGAgentClient([
    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

$satispayGAgentClient->authentication->authenticate('33YGWF');

if ($satispayGAgentClient->authentication->ready()) {
    $publicKey = $satispayGAgentClient->authentication->publicKey;
    $privateKey = $satispayGAgentClient->authentication->privateKey;
    $keyId = $satispayGAgentClient->authentication->keyId;

    file_put_contents(
        '_authentication.json',
        json_encode([
            'public_key' => $publicKey,
            'private_key' => $privateKey,
            'key_id' => $keyId
        ], JSON_PRETTY_PRINT)
    );
}
