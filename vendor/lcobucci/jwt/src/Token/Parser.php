<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Token;

use DateTimeImmutable;
use Lcobucci\JWT\Decoder;
use Lcobucci\JWT\Parser as ParserInterface;
use Lcobucci\JWT\Token as TokenInterface;

use function array_key_exists;
use function count;
use function explode;
use function is_array;
use function is_numeric;
use function number_format;

final class Parser implements ParserInterface
{
    private const MICROSECOND_PRECISION = 6;

    public function __construct(private readonly Decoder $decoder)
    {
    }

    public function parse(string $jwt): TokenInterface
    {
        [$encodedHeaders, $encodedClaims, $encodedSignature] = $this->splitJwt($jwt);

        if ($encodedHeaders === '') {
            throw InvalidTokenStructure::missingHeaderPart();
        }

        if ($encodedClaims === '') {
            throw InvalidTokenStructure::missingClaimsPart();
        }

        if ($encodedSignature === '') {
            throw InvalidTokenStructure::missingSignaturePart();
        }

        $header = $this->parseHeader($encodedHeaders);

        return new Plain(
            new DataSet($header, $encodedHeaders),
            new DataSet($this->parseClaims($encodedClaims), $encodedClaims),
            $this->parseSignature($encodedSignature),
        );
    }

    /**
     * Splits the JWT string into an array
     *
     * @param non-empty-string $jwt
     *
     * @return string[]
     *
     * @throws InvalidTokenStructure When JWT doesn't have all parts.
     */
    private function splitJwt(string $jwt): array
    {
        $data = explode('.', $jwt);

        if (count($data) !== 3) {
            throw InvalidTokenStructure::missingOrNotEnoughSeparators();
        }

        return $data;
    }

    /**
     * Parses the header from a string
     *
     * @param non-empty-string $data
     *
     * @return array<non-empty-string, mixed>
     *
     * @throws UnsupportedHeaderFound When an invalid header is informed.
     * @throws InvalidTokenStructure  When parsed content isn't an array.
     */
    private function parseHeader(string $data): array
    {
        $header = $this->decoder->jsonDecode($this->decoder->base64UrlDecode($data));

        if (! is_array($header)) {
            throw InvalidTokenStructure::arrayExpected('headers');
        }

        $this->guardAgainstEmptyStringKeys($header, 'headers');

        if (array_key_exists('enc', $header)) {
            throw UnsupportedHeaderFound::encryption();
        }

        if (! array_key_exists('typ', $header)) {
            $header['typ'] = 'JWT';
        }

        return $header;
    }

    /**
     * Parses the claim set from a string
     *
     * @param non-empty-string $data
     *
     * @return array<non-empty-string, mixed>
     *
     * @throws InvalidTokenStructure When parsed content isn't an array or contains non-parseable dates.
     */
    private function parseClaims(string $data): array
    {
        $claims = $this->decoder->jsonDecode($this->decoder->base64UrlDecode($data));

        if (! is_array($claims)) {
            throw InvalidTokenStructure::arrayExpected('claims');
        }

        $this->guardAgainstEmptyStringKeys($claims, 'claims');

        if (array_key_exists(RegisteredClaims::AUDIENCE, $claims)) {
            $claims[RegisteredClaims::AUDIENCE] = (array) $claims[RegisteredClaims::AUDIENCE];
        }

        foreach (RegisteredClaims::DATE_CLAIMS as $claim) {
            if (! array_key_exists($claim, $claims)) {
                continue;
            }

            $claims[$claim] = $this->convertDate($claims[$claim]);
        }

        return $claims;
    }

    /**
     * @param array<string, mixed> $array
     * @param non-empty-string     $part
     *
     * @phpstan-assert array<non-empty-string, mixed> $array
     */
    private function guardAgainstEmptyStringKeys(array $array, string $part): void
    {
        foreach ($array as $key => $value) {
            if ($key === '') {
                throw InvalidTokenStructure::arrayExpected($part);
            }
        }
    }

    /** @throws InvalidTokenStructure */
    private function convertDate(int|float|string $timestamp): DateTimeImmutable
    {
        if (! is_numeric($timestamp)) {
            throw InvalidTokenStructure::dateIsNotParseable($timestamp);
        }

        $normalizedTimestamp = number_format((float) $timestamp, self::MICROSECOND_PRECISION, '.', '');

        $date = DateTimeImmutable::createFromFormat('U.u', $normalizedTimestamp);

        if ($date === false) {
            throw InvalidTokenStructure::dateIsNotParseable($normalizedTimestamp);
        }

        return $date;
    }

    /**
     * Returns the signature from given data
     *
     * @param non-empty-string $data
     */
    private function parseSignature(string $data): Signature
    {
        $hash = $this->decoder->base64UrlDecode($data);

        return new Signature($hash, $data);
    }
}
