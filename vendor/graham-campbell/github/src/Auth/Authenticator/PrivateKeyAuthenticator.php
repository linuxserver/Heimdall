<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub\Auth\Authenticator;

use DateInterval;
use DateTimeImmutable;
use Github\Client;
use InvalidArgumentException;
use Lcobucci\JWT\ClaimsFormatter;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\UnifyAudience;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\RegisteredClaims;

/**
 * This is private key github authenticator.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Pavel Zhytomirsky <r3volut1oner@gmail.com>
 */
final class PrivateKeyAuthenticator extends AbstractAuthenticator
{
    /**
     * Build JWT token from provided private key file and authenticate with it.
     *
     * @param array $config
     *
     * @throws \Exception
     *
     * @return \Github\Client
     */
    public function authenticate(array $config)
    {
        if (!$this->client) {
            throw new InvalidArgumentException('The client instance was not given to the private key authenticator.');
        }

        if (!array_key_exists('appId', $config)) {
            throw new InvalidArgumentException('The private key authenticator requires the application id to be configured.');
        }

        $this->client->authenticate(self::getToken($config)->toString(), Client::AUTH_JWT);

        return $this->client;
    }

    /**
     * Build JWT token from provided private key file.
     *
     * @param array $config
     *
     * @throws \Exception
     *
     * @return \Lcobucci\JWT\Token
     */
    private static function getToken(array $config)
    {
        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            self::getKey($config)
        );

        $issued = (new DateTimeImmutable())
            ->sub(new DateInterval('PT30S'));

        $expires = $issued
            ->add(new DateInterval('PT10M'));

        $builder = self::getBuilder($configuration)
            ->expiresAt($expires)
            ->issuedAt($issued)
            ->issuedBy((string) $config['appId']);

        return $builder->getToken(
            $configuration->signer(),
            $configuration->signingKey()
        );
    }

    /**
     * Get the key from the config.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Lcobucci\JWT\Signer\Key
     */
    private static function getKey(array $config)
    {
        if (
            !(array_key_exists('key', $config) || array_key_exists('keyPath', $config)) ||
            (array_key_exists('key', $config) && array_key_exists('keyPath', $config))
        ) {
            throw new InvalidArgumentException('The private key authenticator requires the key or key path to be configured.');
        }

        if (array_key_exists('key', $config)) {
            return InMemory::plainText($config['key'], $config['passphrase'] ?? '');
        }

        return LocalFileReference::file($config['keyPath'], $config['passphrase'] ?? '');
    }

    /**
     * Create a JWT builder.
     *
     * @param \Lcobucci\JWT\Configuration $configuration
     *
     * @return \Lcobucci\JWT\Builder
     */
    private static function getBuilder(Configuration $configuration)
    {
        if (!interface_exists(ClaimsFormatter::class)) {
            return $configuration->builder();
        }

        $formatter = new class() implements ClaimsFormatter {
            public function formatClaims(array $claims): array
            {
                foreach (RegisteredClaims::DATE_CLAIMS as $claim) {
                    if (!array_key_exists($claim, $claims)) {
                        continue;
                    }

                    $claims[$claim] = $claims[$claim]->getTimestamp();
                }

                return $claims;
            }
        };

        return $configuration->builder(
            new ChainedFormatter(new UnifyAudience(), $formatter)
        );
    }
}
