<?php

namespace EmanueleCoppola\Satispay\Contracts;

use EmanueleCoppola\Satispay\Exceptions\SatispayRSAException;

abstract class RSAServiceContract
{

    /**
     * The public key saved on the Satispay servers.
     *
     * @var string|null
     */
    public string|null $publicKey;

    /**
     * The private key used to sign the requests.
     *
     * @var string|null
     */
    public string|null $privateKey;

    /**
     * The passphrase that must be used for both RSA generation and reading.
     * It is optional.
     *
     * @var string|null
     */
    public string|null $passphrase;

    /**
     * RSAServiceContract constructor.
     *
     * @param string|null $publicKey The public key associated to the private key.
     * @param string|null $privateKey The private key used for authentication.
     * @param string|null $passphrase The passphrase set to the private key.
     */
    public function __construct(string|null $publicKey = null, string|null $privateKey = null, string|null $passphrase = null)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;

        $this->passphrase = $passphrase;
    }

    /**
     * Verifies that this service is available.
     * This because it requires some external libraries / extensions.
     *
     * @return bool
     */
    abstract public function isAvailable(): bool;

    /**
     * Throws an exception if the service is not available in this installation.
     *
     * @throws SatispayRSAException if the service is not available.
     *
     * @return bool
     */
    abstract public function ensureAvailable(): void;

    /**
     * Generate a pair of RSA keys if not already generated.
     *
     * This method ensures the generation of a new pair of RSA keys,
     * consisting of a public key and a private key, only if they
     * have not been generated yet.
     *
     * The generated keys will be of the following specifications:
     * - Digest Algorithm: SHA-256
     * - Private Key Bits: 4096
     *
     * @param int $bits Private key bits.
     *
     * @throws SatispayRSAException If key generation fails.
     *
     * @return void
     */
    abstract public function generateKeys($bits = 4096): void;

    /**
     * Sign a string with the given private key.
     *
     * @throws SatispayRSAException If signing fails.
     *
     * @param string $message The message that will be signed.
     *
     * @return string
     */
    abstract public function sign($message): string;
}
