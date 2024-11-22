<?php

namespace EmanueleCoppola\Satispay\Services\RSAService;

use EmanueleCoppola\Satispay\Contracts\RSAServiceContract;
use EmanueleCoppola\Satispay\Exceptions\SatispayRSAException;

/**
 * Class OpenSSL_RSAService
 *
 * RSA Service implemented via OpenSSL.
 */
class OpenSSL_RSAService extends RSAServiceContract
{

    /**
     * The algorithm used for the signature.
     */
    const ALGORITHM = 'sha256WithRSAEncryption';

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {
        return
            extension_loaded('openssl') &&
            (
                function_exists('openssl_get_md_methods') &&
                in_array(
                    self::ALGORITHM,
                    openssl_get_md_methods(true)
                )
            );
    }

    /**
     * @inheritdoc
     */
    public function ensureAvailable()
    {
        if (!$this->isAvailable()) {
            throw new SatispayRSAException("OpenSSL is not installed!");
        }
    }

    /**
     * @inheritdoc
     */
    public function generateKeys($bits = 4096)
    {
        $pkeyResource = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 4096
        ]);

        if ($pkeyResource === false) {
            throw new SatispayRSAException('Failed to generate private key: ' . openssl_error_string());
        }

        $privateKeyExported = openssl_pkey_export($pkeyResource, $newPrivateKey, $this->passphrase);

        if ($privateKeyExported === false) {
            throw new SatispayRSAException('Failed to export private key: ' . openssl_error_string());
        }

        $pkeyResourceDetails = openssl_pkey_get_details($pkeyResource);

        if ($pkeyResourceDetails === false) {
            throw new SatispayRSAException('Failed to get key details: ' . openssl_error_string());
        }

        $newPublicKey = $pkeyResourceDetails['key'];

        $this->publicKey = $newPublicKey;
        $this->privateKey = $newPrivateKey;
    }

    /**
     * @inheritdoc
     */
    public function sign($string)
    {
        $passphrase = !$this->passphrase ? '' : $this->passphrase;

        $privateKey = openssl_pkey_get_private($this->privateKey, $passphrase);

        openssl_sign($string, $signed, $privateKey, self::ALGORITHM);

        if (!$signed) {
            throw new SatispayRSAException('Signing failed: ' . openssl_error_string());
        }

        return $signed;
    }
}
