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

$noticeNumber = str_pad(mt_rand(0, pow(10, 18) - 1), 18, '0', STR_PAD_LEFT); // random

/**
 * |----------------|--------------------------------------------------------|--------|
 * | Exact tax code | Result                                                 | Code   |
 * |----------------|--------------------------------------------------------|--------|
 * | 00000000000    | mocked payment                                         |   OK   |
 * | 01000000000    | Payment verification already in progress               | PA_025 |
 * | 02000000000    | PAA Payment is duplicate                               | PA_012 |
 * | 03000000000    | PAA Payment not found: vehicle not found               | PA_023 |
 * | 04000000000    | PAA Payment not found: due not computable              | PA_021 |
 * | 05000000000    | PAA Payment not found: period role already existing    | PA_022 |
 * | 06000000000    | PAA Payment not found: already payed                   | PA_019 |
 * | 07000000000    | PAA Payment not found: account holder data unavailable | PA_020 |
 * | 08000000000    | PAA Payment not found                                  | PA_014 |
 * | 09000000000    | Payment not allowed                                    | PA_011 |
 * | 10000000000    | PAA Payment is expired                                 | PA_013 |
 * | 11000000000    | PPT Payment not allowed: unknown domain                | PA_026 |
 * | 12000000000    | PPT Payment not allowed: unreachable station           | PA_028 |
 * | 13000000000    | PPT Payment not allowed: unknown station               | PA_027 |
 * | 14000000000    | PAA Payment is duplicate                               | PA_012 |
 * | 15000000000    | Road Tax invalid region                                | PA_016 |
 * | 16000000000    | Road Tax service off hour                              | PA_017 |
 * | 17000000000    | Road Tax service temporarily unavailable               | PA_018 |
 * | 18000000000    | Multi beneficiary with same iban.                      |   OK   |
 * |----------------|--------------------------------------------------------|--------|
 */

$pagoPaPayment = $satispayGAgentClient->payments->create(
    [
        'request_type' => 'MANUAL',
        'payment_notice_number' => $noticeNumber,
        'domain_id' => '00000000000',
        'amount_unit' => 150.99 * 100, // 150,99€
    ],
    [
        // SatispayHeaders::IDEMPOTENCY_KEY => 'myidempotency'
    ]
);

echo $pagoPaPayment->toJson();
