<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT;

use BadMethodCallException;
use DateTime;
use DateTimeInterface;
use Generator;
use Lcobucci\JWT\Claim\Validatable;
use OutOfBoundsException;

/**
 * Basic structure of the JWT
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 0.1.0
 */
class Token
{
    /**
     * The token headers
     *
     * @var array
     */
    private $headers;

    /**
     * The token claim set
     *
     * @var array
     */
    private $claims;

    /**
     * The token signature
     *
     * @var Signature
     */
    private $signature;

    /**
     * The encoded data
     *
     * @var array
     */
    private $payload;

    /**
     * Initializes the object
     *
     * @param array $headers
     * @param array $claims
     * @param array $payload
     * @param Signature $signature
     */
    public function __construct(
        array $headers = ['alg' => 'none'],
        array $claims = [],
        Signature $signature = null,
        array $payload = ['', '']
    ) {
        $this->headers = $headers;
        $this->claims = $claims;
        $this->signature = $signature;
        $this->payload = $payload;
    }

    /**
     * Returns the token headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns if the header is configured
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasHeader($name)
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * Returns the value of a token header
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     *
     * @throws OutOfBoundsException
     */
    public function getHeader($name, $default = null)
    {
        if ($this->hasHeader($name)) {
            return $this->getHeaderValue($name);
        }

        if ($default === null) {
            throw new OutOfBoundsException('Requested header is not configured');
        }

        return $default;
    }

    /**
     * Returns the value stored in header
     *
     * @param string $name
     *
     * @return mixed
     */
    private function getHeaderValue($name)
    {
        $header = $this->headers[$name];

        if ($header instanceof Claim) {
            return $header->getValue();
        }

        return $header;
    }

    /**
     * Returns the token claim set
     *
     * @return array
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * Returns if the claim is configured
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasClaim($name)
    {
        return array_key_exists($name, $this->claims);
    }

    /**
     * Returns the value of a token claim
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     *
     * @throws OutOfBoundsException
     */
    public function getClaim($name, $default = null)
    {
        if ($this->hasClaim($name)) {
            return $this->claims[$name]->getValue();
        }

        if ($default === null) {
            throw new OutOfBoundsException('Requested claim is not configured');
        }

        return $default;
    }

    /**
     * Verify if the key matches with the one that created the signature
     *
     * @param Signer $signer
     * @param string $key
     *
     * @return boolean
     *
     * @throws BadMethodCallException When token is not signed
     */
    public function verify(Signer $signer, $key)
    {
        if ($this->signature === null) {
            throw new BadMethodCallException('This token is not signed');
        }

        if ($this->headers['alg'] !== $signer->getAlgorithmId()) {
            return false;
        }

        return $this->signature->verify($signer, $this->getPayload(), $key);
    }

    /**
     * Validates if the token is valid
     *
     * @param ValidationData $data
     *
     * @return boolean
     */
    public function validate(ValidationData $data)
    {
        foreach ($this->getValidatableClaims() as $claim) {
            if (!$claim->validate($data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the token is expired.
     *
     * @param DateTimeInterface $now Defaults to the current time.
     *
     * @return bool
     */
    public function isExpired(DateTimeInterface $now = null)
    {
        $exp = $this->getClaim('exp', false);

        if ($exp === false) {
            return false;
        }

        $now = $now ?: new DateTime();

        $expiresAt = new DateTime();
        $expiresAt->setTimestamp($exp);

        return $now > $expiresAt;
    }

    /**
     * Yields the validatable claims
     *
     * @return Generator
     */
    private function getValidatableClaims()
    {
        foreach ($this->claims as $claim) {
            if ($claim instanceof Validatable) {
                yield $claim;
            }
        }
    }

    /**
     * Returns the token payload
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload[0] . '.' . $this->payload[1];
    }

    /**
     * Returns an encoded representation of the token
     *
     * @return string
     */
    public function __toString()
    {
        $data = implode('.', $this->payload);

        if ($this->signature === null) {
            $data .= '.';
        }

        return $data;
    }
}
