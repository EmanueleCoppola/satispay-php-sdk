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

$satispayGBusinessClient->mqtt->authenticate();

if ($satispayGBusinessClient->mqtt->ready()) {
    $clientCertificate = $satispayGBusinessClient->mqtt->clientCertificate;
    $clientCertificateKey = $satispayGBusinessClient->mqtt->clientCertificateKey;
    $shopUid = $satispayGBusinessClient->mqtt->shopUid;

    file_put_contents(
        '_mqtt_authentication.json',
        json_encode([
            'client_certificate' => $clientCertificate,
            'client_certificate_key' => $clientCertificateKey,
            'shop_uid' => $shopUid
        ], JSON_PRETTY_PRINT)
    );

    if (!is_dir('_mqtt')) {
        mkdir('_mqtt', 0755, true);
    }

    // we write these files just because we use php-mqtt which only reads certificates from the file system
    $cert = $satispayGBusinessClient->sandbox() ? 'https://cacerts.digicert.com/pca3-g5.crt.pem' : 'https://www.amazontrust.com/repository/AmazonRootCA1.pem';

    file_put_contents('_mqtt/' . basename($cert), file_get_contents($cert));
    file_put_contents('_mqtt/client_certificate.pem', $clientCertificate);
    file_put_contents('_mqtt/client_certificate.key', $clientCertificateKey);
}

