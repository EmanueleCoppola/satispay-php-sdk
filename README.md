# Satispay PHP SDK

![License](https://img.shields.io/github/license/EmanueleCoppola/satispay-php-sdk)
![PHP Version](https://img.shields.io/badge/php-%3E%3D7.0-8892BF.svg)

This is the most complete PHP SDK for integrating the Satispay APIs.

It provides a comprehensive solution that supports all Satispay API features, allowing for seamless integration of payment functionalities into your PHP applications.

This software is currently mantained by:
- Emanuele Coppola: **[github.com/sponsors/EmanueleCoppola](https://github.com/sponsors/EmanueleCoppola)**

## Table of Contents
- [Get Started](#get-started)
- [Supported APIs](#supported-apis)
- [Usage](#usage)
    - [Client instantiation](#client-instantiation)
    - [Client authentication](#client-authentication)
    - [`SatispayGBusinessClient` payments](#satispaygbusinessclient-payments)
    - [`SatispayGBusinessClient` pre-authorizations](#satispaygbusinessclient-pre-authorizations)
    - [`SatispayGBusinessClient` daily closures](#satispaygbusinessclient-daily-closures)
    - [`SatispayGBusinessClient` consumers](#satispaygbusinessclient-consumers)
    - [`SatispayGBusinessClient` profile](#satispaygbusinessclient-profile)
    - [`SatispayGBusinessClient` reports](#satispaygbusinessclient-reports)
    - [`SatispayGBusinessClient` MQTT](#satispaygbusinessclient-mqtt)
    - [`SatispayGBusinessClient` sessions](#satispaygbusinessclient-sessions)
    - [`SatispayGAgentClient` payments](#satispaygagentclient-payments)
    - [`SatispayGAgentClient` receipts](#satispaygagentclient-receipts)
    - [`SatispayGAgentClient` report requests](#satispaygagentclient-report-requests)
    - [`SatispayGAgentClient` invoices](#satispaygagentclient-invoices)


## Get Started

> **Requires: [PHP 7.0+](https://php.net/releases/), [ext-curl](https://www.php.net/manual/en/book.curl.php), [ext-mbstring](https://www.php.net/manual/en/book.mbstring.php), [ext-json](https://www.php.net/manual/en/book.json.php)**

First, install the SDK via the [Composer](https://getcomposer.org/) package manager:

```bash
composer require emanuelecoppola/satispay-php-sdk
```

If you're using a 7.x PHP version be sure to use a Composer 2.x version that has `composer-runtime-api:^2`.<br>
As per [Composer System Requirements](https://getcomposer.org/doc/00-intro.md#system-requirements):
> Composer in its latest version requires PHP 7.2.5 to run. A long-term-support version (2.2.x) still offers support for PHP 5.3.2+ in case you are stuck with a legacy PHP version. A few sensitive php settings and compile flags are also required, but when using the installer you will be warned about any incompatibilities.


Also ensure that the `php-http/discovery` composer plugin is allowed to run.<br>
This will allow an authomatic PSR-18 HTTP client discovery.


If no PSR-18 HTTP client implementations are available in your project, you can manually install a client:
```bash
composer require guzzlehttp/guzzle
```

Then, you can start interacting with Satispay APIs:

```php
$yourPublicKey = getenv('SATISPAY_PUBLIC_KEY');
$yourPrivateKey = getenv('SATISPAY_PRIVATE_KEY');
$yourKeyId = getenv('SATISPAY_KEY_ID');

$satispayGBusinessClient = new SatispayGBusinessClient([

    // authentication
    // set these three keys only if you are already authenticated
    // if you're not, then follow the authentication example
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

    // authentication
    // set these three keys only if you are already authenticated
    // if you're not, then follow the authentication example
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

// you could either instantiate a SatispayGBusinessClient instance
// or a SatispayGAgentClient instance based on your needs
// the constructor is the same in both classes
$satispayGBusinessClient = new SatispayGBusinessClient([
 
    // authentication
    // set these three keys only if you are already authenticated
    // if you're not, then follow the authentication example
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
        SatispayHeaders::OS_VERSION => 'your os version',

        // the following headers are highly suggested
        SatispayHeaders::APP_SOFTWARE_HOUSE => 'your software house name',
        SatispayHeaders::APP_VERSION  => 'your app version',
        SatispayHeaders::APP_NAME  => 'your app name',

        SatispayHeaders::DEVICE_TYPE  => 'your device type (e.g. POS)',
    ]
]);

```

---

### Client authentication

To authenticate your application, you need to use the 6 character activation code provided by Satispay.<br>
You can read more here: https://developers.satispay.com/docs/credentials

In order to authenticate you can use the following code:


```php
use EmanueleCoppola\Satispay\SatispayGAgentClient;
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

// for the authentication
// you could either instantiate a SatispayGBusinessClient instance
// or a SatispayGAgentClient instance based on your needs
$satispayGBusinessClient = new SatispayGBusinessClient([
    // this is optional, if you use the password in the first authentication
    // you must use it in every instance that you will create with the same credentials
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);

$satispayGBusinessClient->authentication->authenticate('QHPYXD');

$publicKey = $satispayGBusinessClient->authentication->publicKey;
$privateKey = $satispayGBusinessClient->authentication->privateKey;
$keyId = $satispayGBusinessClient->authentication->keyId;

if ($satispayGBusinessClient->authentication->ready()) {
    // store the $publicKey, $privateKey and the $keyId
}

// once done, just reuse them in every client instance to correctly authenticate to the Satispay APIs
// you can find an example below

$newSatispayGBusinessClient = new SatispayGBusinessClient([
    'public_key' => $publicKey,
    'private_key' => $privateKey,
    'key_id' => $keyId,

    // this is optional, if you use the password in the first authentication
    // you must use it in every instance that you will create with the same credentials
    // 'passphrase' => 'my-passphrase',

    'sandbox' => true
]);
```

---

### `SatispayGBusinessClient` payments

Official documentation and code examples:
- [Create payment](https://developers.satispay.com/reference/create-a-payment) -> [code example](examples/GBusiness/payment-create.php)
- [Get payment](https://developers.satispay.com/reference/get-the-details-of-a-payment) -> [code example](examples/GBusiness/payment-get.php)
- [Get shop-payment list](https://developers.satispay.com/reference/get-list-of-payments) -> [code example](examples/GBusiness/payment-all.php)
- [Update payment](https://developers.satispay.com/reference/update-a-payment) -> [code example](examples/GBusiness/payment-update.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;
use EmanueleCoppola\Satispay\SatispayHeaders;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Create payment
$satispayPayment = $satispayGBusinessClient->payments->create(
    [
        'flow' => 'MATCH_CODE',
        'currency' => 'EUR',
        'amount_unit' => 12.99 * 100, // 12,99€
    ],
    [
        // list of headers to be sent
        SatispayHeaders::IDEMPOTENCY_KEY => rand(10, 1000)
    ]
);

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

---

### `SatispayGBusinessClient` pre-authorizations

Official documentation and code examples:
- [Create pre-authorization](https://developers.satispay.com/reference/create-authorization) -> [code example](examples/GBusiness/pre-authorization-create.php)
- [Get pre-authorization](https://developers.satispay.com/reference/get-authorization) -> [code example](examples/GBusiness/pre-authorization-get.php)
- [Update pre-authorization](https://developers.satispay.com/reference/update-authorization) -> [code example](examples/GBusiness/pre-authorization-update.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Create pre-authorization
$satispayPreAuthorization = $satispayGBusinessClient->preAuthorizations->create([
    'reason' => 'Monthly Payments',
    'callback_url' => 'https://myServer.com/myCallbackUrl?payment_id={uuid}',
    'redirect_url' => 'https://myServer.com/myRedirectUrl'
]);

// Get pre-authorization
$satispayPreAuthorization = $satispayGBusinessClient->preAuthorizations->get('9b89c251-6151-4561-93cc-c027f4d7f034');

// Update pre-authorization
$satispayPreAuthorization = $satispayGBusinessClient->preAuthorizations->update(
    '9b89c251-6151-4561-93cc-c027f4d7f034',
    [
        'status' => 'CANCELLED',
    ]
);
```

---

### `SatispayGBusinessClient` daily closures

Official documentation and code examples:
- [Get daily closure](https://developers.satispay.com/reference/retrieve-daily-closure) -> [code example](examples/GBusiness/daily-closure-get.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Get daily closure
$satispayConsumer = $satispayGBusinessClient->dailyClosures->get('20230119', ['generate_pdf' => true]);
```

---

### `SatispayGBusinessClient` consumers

Official documentation and code examples:
- [Get consumer](https://developers.satispay.com/reference/retrive-consumer) -> [code example](examples/GBusiness/consumer-get.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Get consumer
$satispayConsumer = $satispayGBusinessClient->consumers->get('+393337777888');
```

---

### `SatispayGBusinessClient` profile

Official documentation and code examples:
- [Get profile](https://developers.satispay.com/reference/retrieve-shop-profile) -> [code example](examples/GBusiness/profile-me.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Get profile
$satispayProfile = $satispayGBusinessClient->profile->me();
```

---

### `SatispayGBusinessClient` reports

Official documentation and code examples:
- [Create report](https://developers.satispay.com/reference/create-new-report) -> [code example](examples/GBusiness/report-create.php)
- [Get report](https://developers.satispay.com/reference/retrieve-a-report) -> [code example](examples/GBusiness/report-get.php)
- [Get reports](https://developers.satispay.com/reference/get-list-of-reports) -> [code example](examples/GBusiness/report-all.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Create report
$satispayReport = $satispayGBusinessClient->reports->create([
    'type' => 'PAYMENT_FEE',
    'format' => 'CSV',
    'query_type' => 'PAYMENT_DATE_INTERVAL',
    'query_payload' => [
        'payment_date_from' => '2024-01-01',
        'payment_date_to' => '2024-01-31',
        'time_zone' => 'Europe/Rome'
    ]
]);

// Get report
$satispayReport = $satispayGBusinessClient->reports->get('6f90eab2-c900-4e2e-9735-cb306ddf2263');

// Get reports
$satispayReports = $satispayGBusinessClient->reports->all();
```

---

### `SatispayGBusinessClient` MQTT

Official documentation and code examples:
- [Authenticate](https://developers.satispay.com/reference/create-mqtt-certificates) -> [code example](examples/GBusiness/mqtt-authenticate.php)
- [Subscribe](https://developers.satispay.com/reference/topic-subscription) -> [code example](examples/GBusiness/mqtt-connect.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

$satispayGBusinessClient->mqtt->authenticate();

if ($satispayGBusinessClient->mqtt->ready()) {
    $clientCertificate = $satispayGBusinessClient->mqtt->clientCertificate;
    $clientCertificateKey = $satispayGBusinessClient->mqtt->clientCertificateKey;
    $shopUid = $satispayGBusinessClient->mqtt->shopUid;
}

// once done, you can use them in an MQTT client to the Satispay APIs

```

---

### `SatispayGBusinessClient` sessions

The sessions are usually used in combination with MQTT.

Official documentation and code examples:
- [Session create](https://developers.satispay.com/reference/open-session) -> [code example](examples/GBusiness/session-create.php)
- [Session get](https://developers.satispay.com/reference/get-session-details) -> [code example](examples/GBusiness/session-get.php)
- [Session create event](https://developers.satispay.com/reference/create-session-event) -> [code example](examples/GBusiness/session-create-event.php)
- [Session update](https://developers.satispay.com/reference/update-session) -> [code example](examples/GBusiness/session-update.php)

```php
use EmanueleCoppola\Satispay\SatispayGBusinessClient;

$satispayGBusinessClient = new SatispayGBusinessClient([...]);

// Session create
$session = $satispayGBusinessClient->sessions->create([
    'fund_lock_uid' => '0de7f8f2-1aa3-4a50-80bc-5f8fa8b21307',
]);

// Session get
$session = $satispayGBusinessClient->sessions->get('8d1ad3b3-1f1c-4cdb-b874-c91210d2fe8a');

// Session create event
$sessionEvent = $satispayGBusinessClient->sessions->createEvent(
    '8d1ad3b3-1f1c-4cdb-b874-c91210d2fe8a',
    [
        'operation' => 'ADD', // or REMOVE
        'amount_unit' => 2 * 100,
        'currency' => 'EUR'
    ]
);

// Session update
$session = $satispayGBusinessClient->sessions->update(
    '8d1ad3b3-1f1c-4cdb-b874-c91210d2fe8a',
    [
        'action' => 'CLOSE',
    ]
);
```

---

### `SatispayGAgentClient` payments

Official documentation and code examples:
- [Create PagoPA payment](https://connect.satispay.com/reference/create-a-new-pagopa-payment) -> [code example](examples/GAgent/payment-create.php)
- [Get PagoPA payment](https://connect.satispay.com/reference/retrieve-a-payment) -> [code example](examples/GAgent/payment-get.php)
- [Get PagoPA payments](https://connect.satispay.com/reference/retrieve-all-payments) -> [code example](examples/GAgent/payment-all.php)
- [Update PagoPA payment](https://connect.satispay.com/reference/update-a-pagopa-payment) -> [code example](examples/GAgent/payment-update.php)

```php
use EmanueleCoppola\Satispay\SatispayGAgentClient;

$satispayGAgentClient = new SatispayGAgentClient([...]);

// Create PagoPA payment
$pagoPaPayment = $satispayGAgentClient->payments->create(
    [
        'request_type' => 'MANUAL',
        'payment_notice_number' => 184924423482948483,
        'domain_id' => '00000000000',
        'amount_unit' => 150.99 * 100, // 150,99€
    ]
);

// Get PagoPA payment
$pagoPaPayment = $satispayGAgentClient->payments->get('1f4b5397-f60a-4a98-81f0-6f3aef88bb80');

// Get PagoPA payments
$pagoPaPayments = $satispayGAgentClient->payments->all();

// Update PagoPA payment
$pagoPaPayment = $satispayGAgentClient->payments->update(
    '1f4b5397-f60a-4a98-81f0-6f3aef88bb80',
    [
        'status' => 'APPROVED',
    ]
);
```

---

### `SatispayGAgentClient` receipts

Official documentation and code examples:
- [Get PagoPA receipt](https://connect.satispay.com/reference/get-receipt) -> [code example](examples/GAgent/receipt-get.php)

```php
use EmanueleCoppola\Satispay\SatispayGAgentClient;

$satispayGAgentClient = new SatispayGAgentClient([...]);

// Get PagoPA receipt
$satispayPayment = $satispayGAgentClient->receipts->get('1f4b5397-f60a-4a98-81f0-6f3aef88bb80');
```

---

### `SatispayGAgentClient` report requests

Official documentation and code examples:
- [Create PagoPA report request](https://connect.satispay.com/reference/a) -> [code example](examples/GAgent/report-request-create.php)
- [Get PagoPA report request](https://connect.satispay.com/reference/create-a-new-pagopa-report-request-copy) -> [code example](examples/GAgent/report-request-get.php)
- [Get PagoPA report requests](https://connect.satispay.com/reference/retrieve-a-pagopa-report-request-by-id-copy) -> [code example](examples/GAgent/report-request-all.php)

```php
use EmanueleCoppola\Satispay\SatispayGAgentClient;

$satispayGAgentClient = new SatispayGAgentClient([...]);

// Create PagoPA report request
$pagoPaReportRequest = $satispayGAgentClient->reportRequests->create(
    [
        'from' => '2024-11-01',
        'to' => '2024-11-30',
        'time_zone' => 'Europe/Rome',
        'format' => 'CSV'
    ],
    [
        // SatispayHeaders::IDEMPOTENCY_KEY => 'myidempotency'
    ]
);

// Get PagoPA report request
$pagoPaReportRequest = $satispayGAgentClient->reportRequests->get('46808dc9-d36b-470d-b0cd-094b3b63a507');

// Get PagoPA report requests
$pagoPaReportRequests = $satispayGAgentClient->reportRequests->all();

```

---

### `SatispayGAgentClient` invoices

Official documentation and code examples:
- [Create PagoPA invoice](https://connect.satispay.com/reference/retrieve-a-pagopa-invoice-by-id) -> [code example](examples/GAgent/invoice-get.php)
- [Get PagoPA invoices](https://connect.satispay.com/reference/retrieve-all-invoices) -> [code example](examples/GAgent/invoice-all.php)

```php
use EmanueleCoppola\Satispay\SatispayGAgentClient;

$satispayGAgentClient = new SatispayGAgentClient([...]);

// Get PagoPA invoice
$pagoPaInvoice = $satispayGAgentClient->invoices->get('2c79cb93-e48b-42a5-9a53-e7faf32d1f9d');

// Get PagoPA invoices
$pagoPaInvoices = $satispayGAgentClient->invoices->all();
```
