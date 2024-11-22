<?php

namespace EmanueleCoppola\Satispay\Services\RSAService;

use \Exception;
use EmanueleCoppola\Satispay\Contracts\RSAServiceContract;
use EmanueleCoppola\Satispay\Exceptions\SatispayRSAException;

/**
 * Class SeclibRSAService
 *
 * RSA Service implemented via phpseclib 3.
 */
class SeclibRSAService extends RSAServiceContract
{

    /**
     * @inheritdoc
     */
    public function isAvailable(): bool
    {
        return class_exists('phpseclib3\Crypt\RSA');
    }

    /**
     * @inheritdoc
     */
    public function ensureAvailable(): void
    {
        if (!$this->isAvailable()) {
            throw new SatispayRSAException("phpseclib3 is not installed!");
        }
    }

    /**
     * @inheritdoc
     */
    public function generateKeys($bits = 4096): void
    {
        try {
            // Generate the private key
            $privateKey = \phpseclib3\Crypt\RSA::createKey($bits);

            // If a passphrase is provided, set it for the private key
            if ($this->passphrase) {
                $privateKey = $privateKey->withPassword($this->passphrase);
            }

            // Get the public key from the private key
            $publicKey = $privateKey->getPublicKey();

            $this->privateKey = $privateKey->toString('PKCS8');
            $this->publicKey = $publicKey->toString('PKCS8');
        } catch (Exception $e) {
            throw new SatispayRSAException('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function sign($message): string
    {
        try {
            /** @var \phpseclib3\Crypt\RSA\PrivateKey $privateKey */
            $privateKey = \phpseclib3\Crypt\RSA::loadPrivateKey($this->privateKey, $this->passphrase);
            $privateKey = $privateKey
                ->withHash('sha256')
                ->withPadding(\phpseclib3\Crypt\RSA::SIGNATURE_PKCS1);

            return $privateKey->sign($message);
        } catch (Exception $e) {
            throw new SatispayRSAException('Signing failed: ' . $e->getMessage());
        }
    }
}
