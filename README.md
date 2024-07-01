# Satispay PHP SDK

![License](https://img.shields.io/github/license/EmanueleCoppola/satispay-php-sdk)
![PHP Version](https://img.shields.io/badge/php-%3E%3D5.4-8892BF.svg)

This is a PHP SDK for integrating with the Satispay APIs.

It provides a comprehensive solution that supports all Satispay API features, allowing for seamless integration of payment functionalities into your PHP applications.

This software is currently mantained by:
- Emanuele Coppola: **[github.com/sponsors/EmanueleCoppola](https://github.com/sponsors/EmanueleCoppola)**

## Table of Contents
- [Get Started](#get-started)
- [Supported APIs](#supported-apis)
- [Usage](#usage)

## Get Started

> **Requires: [PHP 5.4+](https://php.net/releases/), [ext-curl](https://www.php.net/manual/en/book.curl.php), [ext-mbstring](https://www.php.net/manual/en/book.mbstring.php), [ext-json](https://www.php.net/manual/en/book.json.php)**

First, install the SDK via the [Composer](https://getcomposer.org/) package manager:

```bash
composer require emanuelecoppola/satispay-php-sdk
```

Ensure that the `php-http/discovery` composer plugin is allowed to run or install a client manually if your project does not already have a PSR-18 client integrated.
```bash
composer require guzzlehttp/guzzle
```

Then, you can start interacting with Satispay APIs:

```php
$yourPublicKey = getenv('SATISPAY_PUBLIC_KEY');
$yourPrivateKey = getenv('SATISPAY_PRIVATE_KEY');
$yourKeyId = getenv('SATISPAY_KEY_ID');

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $yourPublicKey,
    'private_key' => $yourPrivateKey,
    'key_id' => $yourKeyId,

    'sandbox' => true
]);

$payment = $satispayGBusinessClient->payments->create([
    'flow' => 'MATCH_CODE',
    'currency' => 'EUR',
    'amount_unit' => 12.99 * 100, // 12,99€
]);

echo $payment->toJson();
```

If necessary, it is possible to specify a different PSR-18 client implementation.

```php
use Psr\Http\Client\ClientInterface;

$yourPublicKey = getenv('SATISPAY_PUBLIC_KEY');
$yourPrivateKey = getenv('SATISPAY_PRIVATE_KEY');
$yourKeyId = getenv('SATISPAY_KEY_ID');

$satispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $yourPublicKey,
    'private_key' => $yourPrivateKey,
    'key_id' => $yourKeyId,

    'psr' => [
        ClientInterface::class => new \GuzzleHttp\Client([])
    ],

    'sandbox' => true
]);

$payment = $satispayGBusinessClient->payments->create([
    'flow' => 'MATCH_CODE',
    'currency' => 'EUR',
    'amount_unit' => 12.99 * 100, // 12,99€
]);

echo $payment->toJson();
```

## Supported APIs

This SDK supports the following APIs:
  - [Business API](https://developers.satispay.com/)
  - [Agent APIs](https://connect.satispay.com/)
