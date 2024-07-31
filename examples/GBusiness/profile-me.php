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

$profile = $satispayGBusinessClient->profile->me()->toArray();

echo "
  ____        _   _
 / ___|  __ _| |_(_)___ _ __   __ _ _   _
 \___ \ / _` | __| / __| '_ \ / _` | | | |
  ___) | (_| | |_| \__ \ |_) | (_| | |_| |
 |____/ \__,_|\__|_|___/ .__/ \__,_|\__, |
                       |_|          |___/

";

echo 'id: ' . $profile['id'] . "\n";
echo 'name: ' . $profile['name'] . "\n";
echo 'model: ' . $profile['model'] . "\n";
echo 'localization: ' . $profile['localization'] . "\n";
echo 'qr: ' . $profile['qr_code_identifier'] . "\n";
echo 'mv: ' . $profile['payment_methods']['meal_voucher'] ? 'enabled' : 'disabled';
