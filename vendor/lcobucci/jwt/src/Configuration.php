<?php
declare(strict_types=1);

namespace Lcobucci\JWT;

use Closure;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Validation\Constraint;

/**
 * Configuration container for the JWT Builder and Parser
 *
 * Serves like a small DI container to simplify the creation and usage
 * of the objects.
 */
final class Configuration
{
    private Parser $parser;
    private Validator $validator;

    /** @var Closure(ClaimsFormatter $claimFormatter): Builder */
    private Closure $builderFactory;

    /** @var Constraint[] */
    private array $validationConstraints;

    /** @param Closure(ClaimsFormatter $claimFormatter): Builder|null $builderFactory */
    private function __construct(
        private readonly Signer $signer,
        private readonly Key $signingKey,
        private readonly Key $verificationKey,
        private readonly Encoder $encoder,
        private readonly Decoder $decoder,
        ?Parser $parser,
        ?Validator $validator,
        ?Closure $builderFactory,
        Constraint ...$validationConstraints,
    ) {
        $this->parser    = $parser ?? new Token\Parser($decoder);
        $this->validator = $validator ?? new Validation\Validator();

        $this->builderFactory = $builderFactory
            ?? static function (ClaimsFormatter $claimFormatter) use ($encoder): Builder {
                return Token\Builder::new($encoder, $claimFormatter);
            };

        $this->validationConstraints = $validationConstraints;
    }

    public static function forAsymmetricSigner(
        Signer $signer,
        Key $signingKey,
        Key $verificationKey,
        Encoder $encoder = new JoseEncoder(),
        Decoder $decoder = new JoseEncoder(),
    ): self {
        return new self(
            $signer,
            $signingKey,
            $verificationKey,
            $encoder,
            $decoder,
            null,
            null,
            null,
        );
    }

    public static function forSymmetricSigner(
        Signer $signer,
        Key $key,
        Encoder $encoder = new JoseEncoder(),
        Decoder $decoder = new JoseEncoder(),
    ): self {
        return new self(
            $signer,
            $key,
            $key,
            $encoder,
            $decoder,
            null,
            null,
            null,
        );
    }

    /**
     * @deprecated Deprecated since v5.5, please use {@see self::withBuilderFactory()} instead
     *
     * @param callable(ClaimsFormatter): Builder $builderFactory
     */
    public function setBuilderFactory(callable $builderFactory): void
    {
        $this->builderFactory = $builderFactory(...);
    }

    /** @param callable(ClaimsFormatter): Builder $builderFactory */
    public function withBuilderFactory(callable $builderFactory): self
    {
        return new self(
            $this->signer,
            $this->signingKey,
            $this->verificationKey,
            $this->encoder,
            $this->decoder,
            $this->parser,
            $this->validator,
            $builderFactory(...),
            ...$this->validationConstraints,
        );
    }

    public function builder(?ClaimsFormatter $claimFormatter = null): Builder
    {
        return ($this->builderFactory)($claimFormatter ?? ChainedFormatter::default());
    }

    public function parser(): Parser
    {
        return $this->parser;
    }

    /** @deprecated Deprecated since v5.5, please use {@see self::withParser()} instead */
    public function setParser(Parser $parser): void
    {
        $this->parser = $parser;
    }

    public function withParser(Parser $parser): self
    {
        return new self(
            $this->signer,
            $this->signingKey,
            $this->verificationKey,
            $this->encoder,
            $this->decoder,
            $parser,
            $this->validator,
            $this->builderFactory,
            ...$this->validationConstraints,
        );
    }

    public function signer(): Signer
    {
        return $this->signer;
    }

    public function signingKey(): Key
    {
        return $this->signingKey;
    }

    public function verificationKey(): Key
    {
        return $this->verificationKey;
    }

    public function validator(): Validator
    {
        return $this->validator;
    }

    /** @deprecated Deprecated since v5.5, please use {@see self::withValidator()} instead */
    public function setValidator(Validator $validator): void
    {
        $this->validator = $validator;
    }

    public function withValidator(Validator $validator): self
    {
        return new self(
            $this->signer,
            $this->signingKey,
            $this->verificationKey,
            $this->encoder,
            $this->decoder,
            $this->parser,
            $validator,
            $this->builderFactory,
            ...$this->validationConstraints,
        );
    }

    /** @return Constraint[] */
    public function validationConstraints(): array
    {
        return $this->validationConstraints;
    }

    /** @deprecated Deprecated since v5.5, please use {@see self::withValidationConstraints()} instead */
    public function setValidationConstraints(Constraint ...$validationConstraints): void
    {
        $this->validationConstraints = $validationConstraints;
    }

    public function withValidationConstraints(Constraint ...$validationConstraints): self
    {
        return new self(
            $this->signer,
            $this->signingKey,
            $this->verificationKey,
            $this->encoder,
            $this->decoder,
            $this->parser,
            $this->validator,
            $this->builderFactory,
            ...$validationConstraints,
        );
    }
}
