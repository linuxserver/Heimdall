# JWT
[![Gitter](https://img.shields.io/badge/GITTER-JOIN%20CHAT%20%E2%86%92-brightgreen.svg?style=flat-square)](https://gitter.im/lcobucci/jwt?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Total Downloads](https://img.shields.io/packagist/dt/lcobucci/jwt.svg?style=flat-square)](https://packagist.org/packages/lcobucci/jwt) [![Latest Stable Version](https://img.shields.io/packagist/v/lcobucci/jwt.svg?style=flat-square)](https://packagist.org/packages/lcobucci/jwt)

![Branch master](https://img.shields.io/badge/branch-master-brightgreen.svg?style=flat-square)
[![Build Status](https://img.shields.io/travis/lcobucci/jwt/master.svg?style=flat-square)](http://travis-ci.org/#!/lcobucci/jwt)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/lcobucci/jwt/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/jwt/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/lcobucci/jwt/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/jwt/?branch=master)

A simple library to work with JSON Web Token and JSON Web Signature (requires PHP 5.6+).
The implementation is based on the [RFC 7519](https://tools.ietf.org/html/rfc7519).

## Installation

Package is available on [Packagist](http://packagist.org/packages/lcobucci/jwt),
you can install it using [Composer](http://getcomposer.org).

```shell
composer require lcobucci/jwt
```

### Dependencies

- PHP 5.6+
- OpenSSL Extension

## Basic usage

### Creating

Just use the builder to create a new JWT/JWS tokens:

```php
use Lcobucci\JWT\Builder;

$time = time();
$token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)
                        ->permittedFor('http://example.org') // Configures the audience (aud claim)
                        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
                        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
                        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
                        ->withClaim('uid', 1) // Configures a new claim, called "uid"
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

We can easily validate if the token is valid (using the previous token and time as example):

```php
use Lcobucci\JWT\ValidationData;

$data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
$data->setIssuer('http://example.com');
$data->setAudience('http://example.org');
$data->setId('4f1g23a12aa');

var_dump($token->validate($data)); // false, because token cannot be used before now() + 60

$data->setCurrentTime($time + 61); // changing the validation time to future

var_dump($token->validate($data)); // true, because current time is between "nbf" and "exp" claims

$data->setCurrentTime($time + 4000); // changing the validation time to future

var_dump($token->validate($data)); // false, because token is expired since current time is greater than exp

// We can also use the $leeway parameter to deal with clock skew (see notes below)
// If token's claimed time is invalid but the difference between that and the validation time is less than $leeway, 
// then token is still considered valid
$dataWithLeeway = new ValidationData($time, 20); 
$dataWithLeeway->setIssuer('http://example.com');
$dataWithLeeway->setAudience('http://example.org');
$dataWithLeeway->setId('4f1g23a12aa');

var_dump($token->validate($dataWithLeeway)); // false, because token can't be used before now() + 60, not within leeway

$dataWithLeeway->setCurrentTime($time + 51); // changing the validation time to future

var_dump($token->validate($dataWithLeeway)); // true, because current time plus leeway is between "nbf" and "exp" claims

$dataWithLeeway->setCurrentTime($time + 3610); // changing the validation time to future but within leeway

var_dump($token->validate($dataWithLeeway)); // true, because current time - 20 seconds leeway is less than exp

$dataWithLeeway->setCurrentTime($time + 4000); // changing the validation time to future outside of leeway

var_dump($token->validate($dataWithLeeway)); // false, because token is expired since current time is greater than exp
```

#### Important

- You have to configure ```ValidationData``` informing all claims you want to validate the token.
- If ```ValidationData``` contains claims that are not being used in token or token has claims that are not
configured in ```ValidationData``` they will be ignored by ```Token::validate()```.
- ```exp```, ```nbf``` and ```iat``` claims are configured by default in ```ValidationData::__construct()```
with the current UNIX time (```time()```).
- The optional ```$leeway``` parameter of ```ValidationData``` will cause us to use that number of seconds of leeway 
when validating the time-based claims, pretending we are further in the future for the "Issued At" (```iat```) and "Not 
Before" (```nbf```) claims and pretending we are further in the past for the "Expiration Time" (```exp```) claim. This
allows for situations where the clock of the issuing server has a different time than the clock of the verifying server, 
as mentioned in [section 4.1 of RFC 7519](https://tools.ietf.org/html/rfc7519#section-4.1).

## Token signature

We can use signatures to be able to verify if the token was not modified after its generation. This library implements Hmac, RSA and ECDSA signatures (using 256, 384 and 512).

### Important

Do not allow the string sent to the Parser to dictate which signature algorithm
to use, or else your application will be vulnerable to a [critical JWT security vulnerability](https://auth0.com/blog/2015/03/31/critical-vulnerabilities-in-json-web-token-libraries).

The examples below are safe because the choice in `Signer` is hard-coded and
cannot be influenced by malicious users.

### Hmac

Hmac signatures are really simple to be used:

```php
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$signer = new Sha256();
$time = time();

$token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)
                        ->permittedFor('http://example.org') // Configures the audience (aud claim)
                        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
                        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
                        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
                        ->withClaim('uid', 1) // Configures a new claim, called "uid"
                        ->getToken($signer, new Key('testing')); // Retrieves the generated token


var_dump($token->verify($signer, 'testing 1')); // false, because the key is different
var_dump($token->verify($signer, 'testing')); // true, because the key is the same
```

### RSA and ECDSA

RSA and ECDSA signatures are based on public and private keys so you have to generate using the private key and verify using the public key:

```php
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256; // you can use Lcobucci\JWT\Signer\Ecdsa\Sha256 if you're using ECDSA keys

$signer = new Sha256();
$privateKey = new Key('file://{path to your private key}');
$time = time();

$token = (new Builder())->issuedBy('http://example.com') // Configures the issuer (iss claim)
                        ->permittedFor('http://example.org') // Configures the audience (aud claim)
                        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
                        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
                        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
                        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
                        ->withClaim('uid', 1) // Configures a new claim, called "uid"
                        ->getToken($signer,  $privateKey); // Retrieves the generated token

$publicKey = new Key('file://{path to your public key}');

var_dump($token->verify($signer, $publicKey)); // true when the public key was generated by the private one =)
```

**It's important to say that if you're using RSA keys you shouldn't invoke ECDSA signers (and vice-versa), otherwise ```sign()``` and ```verify()``` will raise an exception!**
