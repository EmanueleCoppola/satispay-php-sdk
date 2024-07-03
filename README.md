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
    - [Client instantiation](#client-instantiation)
    - [`SatispayGBusinessClient` payments](#satispaygbusinessclient-payments)

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

## Usage

### Client instantiation

```php
use EmanueleCoppola\Satispay\SatispayGAgentClient;
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

use Psr\Http\Client\ClientInterface;

use EmanueleCoppola\Satispay\Services\RSAService\OpenSSL_RSAService;
use EmanueleCoppola\Satispay\Services\RSAService\SeclibRSAService;

use EmanueleCoppola\Satispay\SatispayHeaders;

$yourPublicKey = getenv('SATISPAY_PUBLIC_KEY');
$yourPrivateKey = getenv('SATISPAY_PRIVATE_KEY');
$yourKeyId = getenv('SATISPAY_KEY_ID');

// you cold either instantiate a SatispayGBusinessClient instance
// or a SatispayGAgentClient instance based on your needs
$satispayGBusinessClient = new SatispayGBusinessClient([
    // authentication
    // here you can specify the authentication keys used
    'public_key' => $yourPublicKey,
    'private_key' => $yourPrivateKey,
    'key_id' => $yourKeyId,

    // here you can specify the passphrase set in the RSA keys
    'passphrase' => null,

    // here you can tell which environment to use
    // true for sandbox
    // false for production
    'sandbox' => false,

    // RSA encryption strategy
    // here you can pass an array of RSAServiceContract implementations
    // by default are like this:
    'rsa_service' => [
        OpenSSL_RSAService::class,
        SeclibRSAService::class
    ],

    // custom PSR interfaces
    // here you can specify all the custom PSR interfaces that you want to user
    // by default each instance will be resolved from the dependencies
    'psr' => [
        ClientInterface::class => new \GuzzleHttp\Client([])
    ],

    // headers
    // here you can specify more details about your integration
    // to better help Satispay technician to investigate in your issues
    'headers' => [

        // the two OS_ headers have a default value set by reading your PHP installation
        SatispayHeaders::OS => 'your os',
        SatispayHeaders::OS_VERSION => 'your os versione',

        // the following headers are highly suggested
        SatispayHeaders::APP_SOFTWARE_HOUSE => 'your software house name',
        SatispayHeaders::APP_VERSION  => 'your app version',
        SatispayHeaders::APP_NAME  => 'your app name',

        SatispayHeaders::DEVICE_TYPE  => 'your device type (e.g. POS)',
    ]
]);

```


### `SatispayGBusinessClient` payments

Official documentation and code examples:
- [Create payment](https://developers.satispay.com/reference/create-a-payment) -> [code example](examples/GBusiness/payment-create.php)
- [Get payment](https://developers.satispay.com/reference/get-the-details-of-a-payment) -> [code example](examples/GBusiness/payment-get.php)
- [Get shop-payment list](https://developers.satispay.com/reference/get-list-of-payments) -> [code example](examples/GBusiness/payment-all.php)
- [Update payment](https://developers.satispay.com/reference/update-a-payment) -> [code example](examples/GBusiness/payment-update.php)

```php
$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Create payment
$satispayPayment = $satispayGBusinessClient->payments->create([
    'flow' => 'MATCH_CODE',
    'currency' => 'EUR',
    'amount_unit' => 12.99 * 100, // 12,99€
]);

// Get payment
$satispayPayment = $satispayGBusinessClient->payments->get('7b5g32m5-3166-4c01-4617-edrb41558ce7');

// Get payment list
$satispayPayments = $satispayGBusinessClient->payments->all([
    'status' => 'ACCEPTED'
]);

// Update payment
$satispayPayment = $satispayGBusinessClient->payments->update(
    '7b5g32m5-3166-4c01-4617-edrb41558ce7',
    [
        'action' => 'ACCEPT',
    ]
);
```

### `SatispayGBusinessClient` consumers

Official documentation and code examples:
- [Get consumer](https://developers.satispay.com/reference/retrive-consumer) -> [code example](examples/GBusiness/consumer-get.php)

```php
$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Get payment
$satispayConsumer = $satispayGBusinessClient->consumers->get('+393337777888');
```

