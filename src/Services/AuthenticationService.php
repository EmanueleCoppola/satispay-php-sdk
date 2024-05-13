<?php

namespace Satispay\Services;

use Satispay\Exceptions\SatispayException;

/**
 * Class AuthenticationService
 *
 * Service class to authenticate to the APIs and to sign authentication strings.
 */
class AuthenticationService extends BaseService {
    
    /**
     * The public key saved on the Satispay servers.
     *
     * @var string|null
     */
    public $publicKey;

    /**
     * The private key used to sign the requests.
     *
     * @var OpenSSLAsymmetricKey|string|null
     */
    public $privateKey;

    /**
     * The key ID given by Satispay.
     *
     * @var string|null
     */
    public $keyId;

    /**
     * The passphrase that must be used for both RSA generation and reading.
     * This parameter is optional.
     *
     * @var string|null
     */
    public $passphrase;

    /**
     * AuthenticationService constructor.
     *
     * @param SatispayClient $context The SatispayClient context for the service.
     *
     * @param string $publicKey The already generated public key.
     * @param string $privateKey The already generated private key.
     * @param string $keyId The already generated KeyID.
     * @param string $passphrase The optional RSA passphrase.
     *
     * @throws SatispayException If OpenSSL extension is not loaded.
     */
    public function __construct(
        $context = null,

        $publicKey = null,
        $privateKey = null,
        $keyId = null,

        $passphrase = null
    ) {
        $this->ensureOpenSslExtensionLoaded();
    
        $this->context = $context;

        $this->passphrase = $passphrase;

        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey ? openssl_pkey_get_private($privateKey, !$this->passphrase ? '' : $this->passphrase) : null;

        $this->keyId = $keyId;
    }

    /**
     * Check if the OpenSSL extension is actively loaded in the PHP environment.
     *
     * @throws SatispayException If the OpenSSL extension is not loaded.
     *
     * @return void
     */
    private function ensureOpenSslExtensionLoaded()
    {
        if (!extension_loaded('openssl')) {
            throw new SatispayException("OpenSSL is not installed!");
        }
    }

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
     * After successful generation, the keys are assigned to the
     * corresponding properties of the class.
     *
     * @return void
     */
    private function generateKeys()
    {
    	if (empty($this->publicKey) || empty($this->privateKey)) {
            $pkeyResource = openssl_pkey_new([
                'digest_alg' => 'sha256',
                'private_key_bits' => 4096
            ]);

            openssl_pkey_export($pkeyResource, $newPrivateKey, $this->passphrase);

            $pkeyResourceDetails = openssl_pkey_get_details($pkeyResource);

            $newPublicKey = $pkeyResourceDetails['key'];

            $this->publicKey = $newPublicKey;
            $this->privateKey = $newPrivateKey;
    	}
    }

    /**
     * Check if the authentication parameters are set.
     *
     * This method verifies whether the necessary authentication parameters such as public key,
     * private key, and key ID are set, indicating a possible successful authentication.
     * Returns true if all parameters are set, false otherwise.
     *
     * @return bool
     */
    public function ready()
    {
        return !(
            empty($this->publicKey) || empty($this->privateKey) || empty($this->keyId)
        );
    }

    /**
     * Sign a string using the private key with SHA-256 algorithm.
     *
     * This method signs the given string using the class's private key
     * and the SHA-256 hashing algorithm. The resulting signature is returned.
     *
     * @param string $string The string to be signed.
     *
     * @return string The generated signature.
     */
    public function sign(string $string)
    {
        openssl_sign($string, $signed, $this->privateKey, OPENSSL_ALGO_SHA256);

        if (is_null($signed)) {
            throw new SatispayException("Error signing data with the given private key.");
        }

        return $signed;
    }

    /**
     * Authenticates the client by generating keys and sending a request to associate the provided token with the generated public key.
     *
     * @see https://developers.satispay.com/reference/keyid
     *
     * @param string $token The authentication token to be associated.
     * @param array $headers Additional headers to include in the HTTP request (optional).
     *
     * @throws SatispayResponseException If an issue occurs during the authentication.
     *
     * @return $this
     */
    public function authenticate($token, $headers = [])
    {
        $this->generateKeys();

        $response = $this->context->http->post(
            '/g_business/v1/authentication_keys',
            [
                'public_key' => $this->publicKey,
                'token' => $token
            ],
            false,
            $headers
        );

        $response->checkExceptions([
            400 => [
                132 => "An invalid RSA key has been sent.",
            ],
            403 => [
                45 => [
                    "The activation code you sent has been already used.",
                    "Remember, activation codes are one-time use."
                ]
            ],
            404 => [
                41 => [
                    "The activation token you sent doesn't exists.",
                    "Please check that you're using it in the right environment."
                ]
            ]
        ]);

        $response = $response->toArray();

        if (key_exists('key_id', $response)) {
            $this->keyId = $response['key_id'];
        }

        return $this;
    }

    /**
     * Authenticates a test request to check that authentication signature and digest are correct.
     *
     * @see https://developers.satispay.com/reference/testinput
     *
     * @throws SatispayException If this functionality not available, such as in a production environment.
     *
     * @return SatispayResponse
     */
    public function test()
    {
        if (!$this->context->sandbox()) {
            throw new SatispayException("The authentication test is available only in sandbox mode.");
        }

        // we could have used a GET request to test
        // but by using a POST we can check that more signature functionalities are working
        // $this->context->http->get('/wally-services/protocol/tests/signature');

        return $this->context->http->post(
            '/wally-services/protocol/tests/signature',
            [
                'flow' => 'MATCH_CODE',
                'amount_unit' => 100,
                'currency' => 'EUR'
            ]
        );
    }
}
