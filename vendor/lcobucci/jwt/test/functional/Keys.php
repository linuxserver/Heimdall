<?php
namespace Lcobucci\JWT;

use Lcobucci\JWT\Signer\Keychain;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
trait Keys
{
    /**
     * @var array
     */
    protected static $rsaKeys;

    /**
     * @var array
     */
    protected static $ecdsaKeys;

    /**
     * @beforeClass
     */
    public static function createRsaKeys()
    {
        $keychain = new Keychain();
        $dir = 'file://' . __DIR__;

        static::$rsaKeys = [
            'private' => $keychain->getPrivateKey($dir . '/rsa/private.key'),
            'public' => $keychain->getPublicKey($dir . '/rsa/public.key'),
            'encrypted-private' => $keychain->getPrivateKey($dir . '/rsa/encrypted-private.key', 'testing'),
            'encrypted-public' => $keychain->getPublicKey($dir . '/rsa/encrypted-public.key')
        ];
    }

    /**
     * @beforeClass
     */
    public static function createEcdsaKeys()
    {
        $keychain = new Keychain();
        $dir = 'file://' . __DIR__;

        static::$ecdsaKeys = [
            'private' => $keychain->getPrivateKey($dir . '/ecdsa/private.key'),
            'private-params' => $keychain->getPrivateKey($dir . '/ecdsa/private2.key'),
            'public1' => $keychain->getPublicKey($dir . '/ecdsa/public1.key'),
            'public2' => $keychain->getPublicKey($dir . '/ecdsa/public2.key'),
            'public-params' => $keychain->getPublicKey($dir . '/ecdsa/public3.key'),
        ];
    }
}
