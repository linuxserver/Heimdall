<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Signer\Key;

use Lcobucci\JWT\Signer\InvalidKeyProvided;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\SodiumBase64Polyfill;
use SplFileObject;
use Throwable;

use function assert;
use function is_string;

final class InMemory implements Key
{
    /** @param non-empty-string $contents */
    private function __construct(public readonly string $contents, public readonly string $passphrase)
    {
    }

    /** @param non-empty-string $contents */
    public static function plainText(string $contents, string $passphrase = ''): self
    {
        self::guardAgainstEmptyKey($contents);

        return new self($contents, $passphrase);
    }

    /** @param non-empty-string $contents */
    public static function base64Encoded(string $contents, string $passphrase = ''): self
    {
        $decoded = SodiumBase64Polyfill::base642bin(
            $contents,
            SodiumBase64Polyfill::SODIUM_BASE64_VARIANT_ORIGINAL,
        );

        self::guardAgainstEmptyKey($decoded);

        return new self($decoded, $passphrase);
    }

    /**
     * @param non-empty-string $path
     *
     * @throws FileCouldNotBeRead
     */
    public static function file(string $path, string $passphrase = ''): self
    {
        try {
            $file = new SplFileObject($path);
        } catch (Throwable $exception) {
            throw FileCouldNotBeRead::onPath($path, $exception);
        }

        $fileSize = $file->getSize();
        $contents = $fileSize > 0 ? $file->fread($file->getSize()) : '';
        assert(is_string($contents));

        self::guardAgainstEmptyKey($contents);

        return new self($contents, $passphrase);
    }

    /** @phpstan-assert non-empty-string $contents */
    private static function guardAgainstEmptyKey(string $contents): void
    {
        if ($contents === '') {
            throw InvalidKeyProvided::cannotBeEmpty();
        }
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function passphrase(): string
    {
        return $this->passphrase;
    }
}
