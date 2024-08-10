<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayGBusinessClient;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;

if (!file_exists('_authentication.json')) die('_authentication.json file not available!');
if (!file_exists('_mqtt_authentication.json')) die('_mqtt_authentication.json file not available!');

$authentication = json_decode(file_get_contents('_authentication.json'), true);
$mqtt_authentication = json_decode(file_get_contents('_mqtt_authentication.json'), true);

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    // MQTT
    'mqtt_certificate_pem' => $mqtt_authentication['client_certificate'],
    'mqtt_private_key' => $mqtt_authentication['client_certificate_key'],
    'mqtt_shop_uid' => $mqtt_authentication['shop_uid'],

    'sandbox' => true
]);

if (
    !$satispayGBusinessClient->mqtt->ready() ||
    !file_exists('_mqtt/AmazonRootCA1.pem') || !file_exists('_mqtt/client_certificate.pem') || !file_exists('_mqtt/client_certificate.key')
) die('MQTT certificate files not found!');

try {
    $client = new MqttClient(
        $satispayGBusinessClient->mqtt->host,
        $satispayGBusinessClient->mqtt->port,
        $satispayGBusinessClient->mqtt->clientId()
    );

    $connectionSettings = (new ConnectionSettings)
        ->setUseTls(true)
        // ->setTlsVerifyPeer(false)
        ->setTlsCertificateAuthorityFile(realpath('_mqtt/AmazonRootCA1.pem'))
        ->setTlsClientCertificateFile(realpath('_mqtt/client_certificate.pem'))
        ->setTlsClientCertificateKeyFile(realpath('_mqtt/client_certificate.key'));

    $client->connect($connectionSettings, true);

    $topic = $satispayGBusinessClient->mqtt->fundLockTopic();

    $client->subscribe($topic, function ($topic, $message, $retained) use ($logger, $client) {
        echo sprintf(
            "We received a %s on topic [%s]: %s",
            $retained ? 'retained message' : 'message',
            $topic,
            $message
        );

        // quit after first message
        $client->interrupt();
    }, MqttClient::QOS_AT_LEAST_ONCE);

    $client->loop(true);

    $client->disconnect();
} catch (MqttClientException $e) {
    echo "Connecting with TLS or publishing with QoS 0 failed. An exception occurred. Exception: " . $e;
}
