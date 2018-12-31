<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer;

/**
 * A utilitarian class that encapsulates the retrieval of public and private keys
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 *
 * @deprecated Since we've removed OpenSSL from ECDSA there's no reason to use this class
 */
class Keychain
{
    /**
     * Returns a private key from file path or content
     *
     * @param string $key
     * @param string $passphrase
     *
     * @return Key
     */
    public function getPrivateKey($key, $passphrase = null)
    {
        return new Key($key, $passphrase);
    }

    /**
     * Returns a public key from file path or content
     *
     * @param string $certificate
     *
     * @return Key
     */
    public function getPublicKey($certificate)
    {
        return new Key($certificate);
    }
}
