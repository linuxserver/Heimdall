<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class IdentifiedBy implements Constraint
{
    /** @param non-empty-string $id */
    public function __construct(private readonly string $id)
    {
    }

    public function assert(Token $token): void
    {
        if (! $token->isIdentifiedBy($this->id)) {
            throw ConstraintViolation::error(
                'The token is not identified with the expected ID',
                $this,
            );
        }
    }
}
