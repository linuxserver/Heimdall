# JWT
[![Gitter](https://img.shields.io/badge/GITTER-JOIN%20CHAT%20%E2%86%92-brightgreen.svg?style=flat-square)](https://gitter.im/lcobucci/jwt?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Total Downloads](https://img.shields.io/packagist/dt/lcobucci/jwt.svg?style=flat-square)](https://packagist.org/packages/lcobucci/jwt) [![Latest Stable Version](https://img.shields.io/packagist/v/lcobucci/jwt.svg?style=flat-square)](https://packagist.org/packages/lcobucci/jwt)

![Branch master](https://img.shields.io/badge/branch-master-brightgreen.svg?style=flat-square)
[![Build Status](https://img.shields.io/travis/lcobucci/jwt/master.svg?style=flat-square)](http://travis-ci.org/#!/lcobucci/jwt)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/lcobucci/jwt/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/jwt/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/lcobucci/jwt/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/jwt/?branch=master)

A simple library to work with JSON Web Token and JSON Web Signature (requires PHP 5.5+).
The implementation is based on the [current draft](http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-32).

## Installation

Package is available on [Packagist](http://packagist.org/packages/lcobucci/jwt),
you can install it using [Composer](http://getcomposer.org).

```shell
composer require lcobucci/jwt
```

### Dependencies

- PHP 5.5+
- OpenSSL Extension

## Basic usage

### Creating

Just use the builder to create a new JWT/JWS tokens:

```php
use Lcobucci\JWT\Builder;

$token = (new Builder())->setIssuer('http://example.com') // Configures the issuer (iss claim)
                        ->setAudience('http://example.org') // Configures the audience (aud claim)
                        ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
                        ->set('uid', 1) // Configures a new claim, called "uid"
                        ->getToken(); // Retrieves the generated token


$token->getHeaders(); // Retrieves the token headers
$token->getClaims(); // Retrieves the token claims

echo $token->getHeader('jti'); // will print "4f1g23a12aa"
echo $token->getClaim('iss'); // will print "http://example.com"
echo $token->getClaim('uid'); // will print "1"
echo $token; // The string representation of the object is a JWT string (pretty easy, right?)
```

### Parsing from strings

Use the parser to create a new token from a JWT string (using the previous token as example):

```php
use Lcobucci\JWT\Parser;

$token = (new Parser())->parse((string) $token); // Parses from a string
$token->getHeaders(); // Retrieves the token header
$token->getClaims(); // Retrieves the token claims

echo $token->getHeader('jti'); // will print "4f1g23a12aa"
echo $token->getClaim('iss'); // will print "http://example.com"
echo $token->getClaim('uid'); // will print "1"
```

### Validating

We can easily validate if the token is valid (using the previous token as example):

```php
use Lcobucci\JWT\ValidationData;

$data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
$data->setIssuer('http://example.com');
$data->setAudience('http://example.org');
$data->setId('4f1g23a12aa');

var_dump($token->validate($data)); // false, because we created a token that cannot be used before of `time() + 60`

$data->setCurrentTime(time() + 60); // changing the validation time to future

var_dump($token->validate($data)); // true, because validation information is equals to data contained on the token

$data->setCurrentTime(time() + 4000); // changing the validation time to future

var_dump($token->validate($data)); // false, because token is expired since current time is greater than exp
```

## Token signature

We can use signatures to be able to verify if the token was not modified after its generation. This library implements Hmac, RSA and ECDSA signatures (using 256, 384 and 512).

### Hmac

Hmac signatures are really simple to be used:

```php
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$signer = new Sha256();

$token = (new Builder())->setIssuer('http://example.com') // Configures the issuer (iss claim)
                        ->setAudience('http://example.org') // Configures the audience (aud claim)
                        ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
                        ->set('uid', 1) // Configures a new claim, called "uid"
                        ->sign($signer, 'testing') // creates a signature using "testing" as key
                        ->getToken(); // Retrieves the generated token


var_dump($token->verify($signer, 'testing 1')); // false, because the key is different
var_dump($token->verify($signer, 'testing')); // true, because the key is the same
```

### RSA and ECDSA

RSA and ECDSA signatures are based on public and private keys so you have to generate using the private key and verify using the public key:

```php
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain; // just to make our life simpler
use Lcobucci\JWT\Signer\Rsa\Sha256; // you can use Lcobucci\JWT\Signer\Ecdsa\Sha256 if you're using ECDSA keys

$signer = new Sha256();

$keychain = new Keychain();

$token = (new Builder())->setIssuer('http://example.com') // Configures the issuer (iss claim)
                        ->setAudience('http://example.org') // Configures the audience (aud claim)
                        ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                        ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
                        ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
                        ->set('uid', 1) // Configures a new claim, called "uid"
                        ->sign($signer,  $keychain->getPrivateKey('file://{path to your private key}')) // creates a signature using your private key
                        ->getToken(); // Retrieves the generated token


var_dump($token->verify($signer, $keychain->getPublicKey('file://{path to your public key}'))); // true when the public key was generated by the private one =)
```

**It's important to say that if you're using RSA keys you shouldn't invoke ECDSA signers (and vice-versa), otherwise ```sign()``` and ```verify()``` will raise an exception!**
