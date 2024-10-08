<?php

namespace Yampi\Api;

use Yampi\Api\Exceptions\RequestException;
use Yampi\Api\Exceptions\InvalidTokenTypeException;

class AuthRequest extends Request
{
    protected $authToken;

    protected $authTokenType;

    protected $user;

    protected $jwtExpiresIn;

    /**
     * Gets the User logged in
     *
     * @return array
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $token
     */
    public function setUserToken(string $token)
    {
        $this->configureAuthToken('user-token', $token);

        return $this;
    }

    public function setJwt(string $jwt)
    {
        $this->configureAuthToken('bearer', $jwt);

        return $this;
    }

    /**
     * Gets the auth token.
     *
     * @return string|null
     */
    public function getAuthToken() : ?string
    {
        return $this->authToken;
    }

    /**
     * Gets the auth token type.
     *
     * @return string|null 'bearer' for jwt or 'user-token' for user-provided token
     */
    public function getAuthTokenType() : ?string
    {
        return $this->authTokenType;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $mfaCode = ''
     *
     * @throws RequestException if anything gets wrong.
     */
    public function login(string $username, string $password, string $mfaCode = '')
    {
        $response = $this
            ->setRoute('auth/login')
            ->post([
                'email' => $username,
                'password' => $password,
                'mfa_code' => $mfaCode,
            ])
            ->getResponse();

        $this->configureAuthToken($response['token_type'], $response['access_token']);
        $this->setUser($response['user']);
        $this->setJwtExpiresIn($response['expires_in']);

        return $this;
    }

    /**
     * Set JWT expires value
     *
     * @param int $value | seconds
     * @return void
     */
    protected function setJwtExpiresIn($value)
    {
        $this->jwtExpiresIn = $value;

        return $this;
    }

    /**
     * getJwtExpiresIn
     *
     * @return int | seconds
     */
    public function getJwtExpiresIn()
    {
        return $this->jwtExpiresIn;
    }

    /**
     * Set the User logged in
     *
     * @param array $value
     */
    protected function setUser($value)
    {
        $this->user = $value;

        return $this;
    }

    /**
     * Configure the authentitation token and type.
     *
     * @param string $type The token type to configure. May be 'bearer' or 'user-token'
     * @param string $token The token to configure
     * @return self
     */
    protected function configureAuthToken(string $type, string $token)
    {
        $availableTypes = ['bearer', 'user-token'];

        if (!in_array($type, $availableTypes)) {
            throw new InvalidTokenTypeException('Invalid Token type. Available: ' . implode(', ', $availableTypes));
        }

        $this->setAuthToken($token);
        $this->setAuthTokenType($type);

        $this->addHeader(
            $type === 'bearer' ? 'Authorization' : 'User-Token',
            $type === 'bearer' ? "Bearer {$token}" : $token
        );

        return $this;
    }

    /**
     * Sets the auth token.
     *
     * @param string $token
     * @return self
     */
    protected function setAuthToken($token)
    {
        $this->authToken = $token;

        return $this;
    }

    /**
     * Sets the auth token type.
     *
     * @param string $type 'bearer' (JWT) or 'token'
     */
    protected function setAuthTokenType($type)
    {
        $this->authTokenType = $type;

        return $this;
    }
}
