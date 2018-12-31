<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer\Ecdsa;

use InvalidArgumentException;
use Lcobucci\JWT\Signer\Key;
use Mdanter\Ecc\Math\MathAdapterInterface;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\PrivateKeySerializerInterface;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface;

/**
 * Base class for ECDSA signers
 *
 * @internal
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 3.0.4
 */
class KeyParser
{
    /**
     * @var PrivateKeySerializerInterface
     */
    private $privateKeySerializer;

    /**
     * @var PublicKeySerializerInterface
     */
    private $publicKeySerializer;

    /**
     * @param MathAdapterInterface $adapter
     * @param PrivateKeySerializerInterface $privateKeySerializer
     * @param PublicKeySerializerInterface $publicKeySerializer
     */
    public function __construct(
        MathAdapterInterface $adapter,
        PrivateKeySerializerInterface $privateKeySerializer = null,
        PublicKeySerializerInterface $publicKeySerializer = null
    ) {
        $this->privateKeySerializer = $privateKeySerializer ?: new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
        $this->publicKeySerializer = $publicKeySerializer ?: new PemPublicKeySerializer(new DerPublicKeySerializer($adapter));
    }

    /**
     * Parses a public key from the given PEM content
     *
     * @param Key $key
     *
     * @return \Mdanter\Ecc\Crypto\Key\PublicKeyInterface
     */
    public function getPublicKey(Key $key)
    {
        return $this->publicKeySerializer->parse($this->getKeyContent($key, 'PUBLIC KEY'));
    }

    /**
     * Parses a private key from the given PEM content
     *
     * @param Key $key
     *
     * @return \Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
     */
    public function getPrivateKey(Key $key)
    {
        return $this->privateKeySerializer->parse($this->getKeyContent($key, 'EC PRIVATE KEY'));
    }

    /**
     * Extracts the base 64 value from the PEM certificate
     *
     * @param Key $key
     * @param string $header
     *
     * @return string
     *
     * @throws InvalidArgumentException When given key is not a ECDSA key
     */
    private function getKeyContent(Key $key, $header)
    {
        $match = null;

        preg_match(
            '/[\-]{5}BEGIN ' . $header . '[\-]{5}(.*)[\-]{5}END ' . $header . '[\-]{5}/',
            str_replace([PHP_EOL, "\n", "\r"], '', $key->getContent()),
            $match
        );

        if (isset($match[1])) {
            return $match[1];
        }

        throw new InvalidArgumentException('This is not a valid ECDSA key.');
    }
}
