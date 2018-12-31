<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer;

use Lcobucci\JWT\Signer\Ecdsa\KeyParser;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Math\MathAdapterInterface as Adapter;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Random\RandomNumberGeneratorInterface;

/**
 * Base class for ECDSA signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
abstract class Ecdsa extends BaseSigner
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * @var KeyParser
     */
    private $parser;

    /**
     * @param Adapter $adapter
     * @param EcdsaSigner $signer
     * @param KeyParser $parser
     */
    public function __construct(Adapter $adapter = null, Signer $signer = null, KeyParser $parser = null)
    {
        $this->adapter = $adapter ?: EccFactory::getAdapter();
        $this->signer = $signer ?: EccFactory::getSigner($this->adapter);
        $this->parser = $parser ?: new KeyParser($this->adapter);
    }

    /**
     * {@inheritdoc}
     */
    public function createHash(
        $payload,
        Key $key,
        RandomNumberGeneratorInterface $generator = null
    ) {
        $privateKey = $this->parser->getPrivateKey($key);
        $generator = $generator ?: RandomGeneratorFactory::getRandomGenerator();

        return $this->createSignatureHash(
            $this->signer->sign(
                $privateKey,
                $this->createSigningHash($payload),
                $generator->generate($privateKey->getPoint()->getOrder())
            )
        );
    }

    /**
     * Creates a binary signature with R and S coordinates
     *
     * @param Signature $signature
     *
     * @return string
     */
    private function createSignatureHash(Signature $signature)
    {
        $length = $this->getSignatureLength();

        return pack(
            'H*',
            sprintf(
                '%s%s',
                str_pad($this->adapter->decHex($signature->getR()), $length, '0', STR_PAD_LEFT),
                str_pad($this->adapter->decHex($signature->getS()), $length, '0', STR_PAD_LEFT)
            )
        );
    }

    /**
     * Creates a hash using the signer algorithm with given payload
     *
     * @param string $payload
     *
     * @return int|string
     */
    private function createSigningHash($payload)
    {
        return $this->adapter->hexDec(hash($this->getAlgorithm(), $payload));
    }

    /**
     * {@inheritdoc}
     */
    public function doVerify($expected, $payload, Key $key)
    {
        return $this->signer->verify(
            $this->parser->getPublicKey($key),
            $this->extractSignature($expected),
            $this->createSigningHash($payload)
        );
    }

    /**
     * Extracts R and S values from given data
     *
     * @param string $value
     *
     * @return \Mdanter\Ecc\Crypto\Signature\Signature
     */
    private function extractSignature($value)
    {
        $length = $this->getSignatureLength();
        $value = unpack('H*', $value)[1];

        return new Signature(
            $this->adapter->hexDec(substr($value, 0, $length)),
            $this->adapter->hexDec(substr($value, $length))
        );
    }

    /**
     * Returns the length of signature parts
     *
     * @internal
     *
     * @return int
     */
    abstract public function getSignatureLength();

    /**
     * Returns the name of algorithm to be used to create the signing hash
     *
     * @internal
     *
     * @return string
     */
    abstract public function getAlgorithm();
}
