<?php

require_once('../../vendor/autoload.php');

use EmanueleCoppola\Satispay\SatispayGBusinessClient;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;

if (!file_exists('_authentication.json')) die('_authentication.json file not available!');
if (!file_exists('_mqtt-authentication.json')) die('_mqtt-authentication.json file not available!');

if (
    !file_exists('_mqtt/client-certificate.pem') || !file_exists('_mqtt/client-certificate.key')
) die('MQTT certificate files not found!');{}

$authentication = json_decode(file_get_contents('_authentication.json'), true);
$mqtt_authentication = json_decode(file_get_contents('_mqtt-authentication.json'), true);

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $authentication['public_key'],
    'private_key' => $authentication['private_key'],
    'key_id' => $authentication['key_id'],

    // The passphrase is needed only if the RSA has been generated with the -passout parameter
    // 'passphrase' => 'my-passphrase',

    // MQTT
    'mqtt_client_certificate' => $mqtt_authentication['client_certificate'],
    'mqtt_client_certificate_key' => $mqtt_authentication['client_certificate_key'],
    'mqtt_shop_uid' => $mqtt_authentication['shop_uid'],

    'sandbox' => true
]);

$cert = basename(
    $satispayGBusinessClient->sandbox() ?
        SatispayGBusinessClient::STAGING_MQTT_CERTIFICATE :
        SatispayGBusinessClient::PRODUCTION_MQTT_CERTIFICATE
);

if (!file_exists('_mqtt/' . $cert)) die($cert . ' file not found!');

try {
    $clientId = $satispayGBusinessClient->mqtt->clientId();

    $client = new MqttClient(
        $satispayGBusinessClient->mqtt->host,
        $satispayGBusinessClient->mqtt->port,
        $clientId,
        MqttClient::MQTT_3_1_1
    );

    $connectionSettings = (new ConnectionSettings)
        ->setUseTls(true)
        // ->setTlsVerifyPeer(false)
        ->setTlsCertificateAuthorityFile(realpath('_mqtt/' . $cert))
        ->setTlsClientCertificateFile(realpath('_mqtt/client-certificate.pem'))
        ->setTlsClientCertificateKeyFile(realpath('_mqtt/client-certificate.key'));

    $client->connect($connectionSettings, true);

    echo "Connected succesfully with client id " . $clientId . ".\n";

    $topic = $satispayGBusinessClient->mqtt->fundLockTopic();

    $client->subscribe($topic, function ($topic, $message, $retained) use ($client) {
        /**
         * $message example:
         *
         * {
         *     "id": "0c16f440-7fdb-46f1-97d9-1e345e68f478",
         *     "event": "FUND_LOCK_STATE_CHANGE",
         *     "payload": {
         *         "uid": "0de7f8f2-1aa3-4a50-80bc-5f8fa8b21307",
         *         "available": true,
         *         "payment_uid": "c0c06415-11c4-471f-8bae-0310a61f9067",
         *         "payment_type": "SHOP_FUND_LOCK",
         *         "currency": "EUR",
         *         "recipient_uid": "0c4f4511-1afa-4b17-b529-ff7465d126c1",
         *         "owner_uid": "1ed62785-4c8d-46c5-8675-5a5d06ea9f67",
         *         "amount_unit": 5500,
         *         "expiration_date": "2024-10-18T18:11:54.160Z",
         *         "insert_date": "2024-10-18T15:11:54.359Z"
         *     },
         *     "correlation_id": "6ikkwcXl",
         *     "date": "2024-10-18T15:11:54.516Z",
         *     "version": "1"
         * }
         *
         * You can use the payload.uid (the fund lock uid) to open a session.
         *
         * Please see the session-create.php example file.
         */

        echo "----------------------------------------------------------------------\n";
        echo sprintf(
            "We received a %s on topic [%s]: %s",
            $retained ? 'retained message' : 'message',
            $topic,
            $message
        ) . "\n";

        $message = json_decode($message, true);

        if(
            array_key_exists('payload', $message) &&
            array_key_exists('uid', $message['payload'])
        ) {
            echo "You can create a session using this id: " . $message['payload']['uid'] . "\n";
        }

        echo "----------------------------------------------------------------------\n";

        // $client->interrupt();
    }, MqttClient::QOS_AT_LEAST_ONCE);

    echo "Subscribed to topic: " . $topic . "\n";

    $client->loop(true);

    $client->disconnect();
} catch (MqttClientException $e) {
    echo "Connecting with TLS or publishing failed. An exception occurred. Exception: " . $e;
}
