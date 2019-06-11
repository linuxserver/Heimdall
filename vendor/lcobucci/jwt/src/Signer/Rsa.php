<?php
/**
 * This file is part of Lcobucci\JWT, a simple library to handle JWT and JWS
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\JWT\Signer;

use const OPENSSL_KEYTYPE_RSA;

/**
 * Base class for RSASSA-PKCS1 signers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @since 2.1.0
 */
abstract class Rsa extends OpenSSL
{
    final public function getKeyType()
    {
        return OPENSSL_KEYTYPE_RSA;
    }
}
