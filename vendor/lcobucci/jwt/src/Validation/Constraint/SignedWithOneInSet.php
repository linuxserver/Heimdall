<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\SignedWith as SignedWithInterface;

use const PHP_EOL;

final class SignedWithOneInSet implements SignedWithInterface
{
    /** @var array<SignedWithUntilDate> */
    private readonly array $constraints;

    public function __construct(SignedWithUntilDate ...$constraints)
    {
        $this->constraints = $constraints;
    }

    public function assert(Token $token): void
    {
        $errorMessage = 'It was not possible to verify the signature of the token, reasons:';

        foreach ($this->constraints as $constraint) {
            try {
                $constraint->assert($token);

                return;
            } catch (ConstraintViolation $violation) {
                $errorMessage .= PHP_EOL . '- ' . $violation->getMessage();
            }
        }

        throw ConstraintViolation::error($errorMessage, $this);
    }
}
