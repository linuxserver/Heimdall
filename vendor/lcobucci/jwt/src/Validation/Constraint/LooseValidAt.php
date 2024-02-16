<?php
declare(strict_types=1);

namespace Lcobucci\JWT\Validation\Constraint;

use DateInterval;
use DateTimeInterface;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\ValidAt as ValidAtInterface;
use Psr\Clock\ClockInterface as Clock;

final class LooseValidAt implements ValidAtInterface
{
    private readonly DateInterval $leeway;

    public function __construct(private readonly Clock $clock, ?DateInterval $leeway = null)
    {
        $this->leeway = $this->guardLeeway($leeway);
    }

    private function guardLeeway(?DateInterval $leeway): DateInterval
    {
        if ($leeway === null) {
            return new DateInterval('PT0S');
        }

        if ($leeway->invert === 1) {
            throw LeewayCannotBeNegative::create();
        }

        return $leeway;
    }

    public function assert(Token $token): void
    {
        $now = $this->clock->now();

        $this->assertIssueTime($token, $now->add($this->leeway));
        $this->assertMinimumTime($token, $now->add($this->leeway));
        $this->assertExpiration($token, $now->sub($this->leeway));
    }

    /** @throws ConstraintViolation */
    private function assertExpiration(Token $token, DateTimeInterface $now): void
    {
        if ($token->isExpired($now)) {
            throw ConstraintViolation::error('The token is expired', $this);
        }
    }

    /** @throws ConstraintViolation */
    private function assertMinimumTime(Token $token, DateTimeInterface $now): void
    {
        if (! $token->isMinimumTimeBefore($now)) {
            throw ConstraintViolation::error('The token cannot be used yet', $this);
        }
    }

    /** @throws ConstraintViolation */
    private function assertIssueTime(Token $token, DateTimeInterface $now): void
    {
        if (! $token->hasBeenIssuedBefore($now)) {
            throw ConstraintViolation::error('The token was issued in the future', $this);
        }
    }
}
