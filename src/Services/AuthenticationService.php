<?php

namespace EmanueleCoppola\Satispay\Services;

use EmanueleCoppola\Satispay\Exceptions\SatispayException;
use EmanueleCoppola\Satispay\SatispayResponse;

/**
 * Class AuthenticationService
 *
 * Service class to authenticate to the APIs and to sign authentication strings.
 *
 * @property-read string|null $publicKey
 * @property-read string|null $privateKey
 */
class AuthenticationService extends BaseService {

    /**
     * The key ID given by Satispay.
     *
     * @var string|null
     */
    public string|null $keyId;

    /**
     * AuthenticationService constructor.
     *
     * @param SatispayClient $context The SatispayClient context for the service.
     * @param string $keyId The already generated KeyID.
     */
    public function __construct(
        $context = null,
        $keyId = null
    ) {
        $this->context = $context;
        $this->keyId = $keyId;
    }

    /**
     * Magic getter to retrieve the publicKey and privateKey from the context's RSA service.
     *
     * @param string $name The name of the property.
     *
     * @return mixed The value of the property.
     */
    public function __get($name)
    {
        if (
            $name === 'publicKey' ||
            $name === 'privateKey'
        ) {
            return $this->context->rsa_service->{$name};
        }

        return $this->{$name};
    }

    /**
     * Check if the authentication parameters are set.
     *
     * This method verifies whether the necessary authentication parameters such as public key,
     * private key, and keyId are set, indicating a possible successful authentication.
     * Returns true if all parameters are set, false otherwise.
     *
     * @return bool
     */
    public function ready(): bool
    {
        // don't touch these two variables
        // magic __isset method should be implemented, but we avoid it
        $publicKey = $this->publicKey;
        $privateKey = $this->privateKey;

        return !(
            empty($publicKey) || empty($privateKey) || empty($this->keyId)
        );
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
    public function authenticate(string $token, array $headers = []): AuthenticationService
    {
        $this->context->rsa_service->generateKeys();

        $response = $this->context->http->post(
            '/g_business/v1/authentication_keys',
            [
                'public_key' => $this->context->rsa_service->publicKey,
                'token' => $token
            ],
            $headers,
            false
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
    public function test(): SatispayResponse
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
