<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Token;

final class Validator implements \Lcobucci\JWT\Validator
{
    public function assert(Token $token, Constraint ...$constraints): void
    {
        if ($constraints === []) {
            throw new NoConstraintsGiven('No constraint given.');
        }

        $violations = [];

        foreach ($constraints as $constraint) {
            $this->checkConstraint($constraint, $token, $violations);
        }

        if ($violations) {
            throw RequiredConstraintsViolated::fromViolations(...$violations);
        }
    }

    /** @param ConstraintViolation[] $violations */
    private function checkConstraint(
        Constraint $constraint,
        Token $token,
        array &$violations,
    ): void {
        try {
            $constraint->assert($token);
        } catch (ConstraintViolation $e) {
            $violations[] = $e;
        }
    }

    public function validate(Token $token, Constraint ...$constraints): bool
    {
        if ($constraints === []) {
            throw new NoConstraintsGiven('No constraint given.');
        }

        try {
            foreach ($constraints as $constraint) {
                $constraint->assert($token);
            }

            return true;
        } catch (ConstraintViolation) {
            return false;
        }
    }
}
