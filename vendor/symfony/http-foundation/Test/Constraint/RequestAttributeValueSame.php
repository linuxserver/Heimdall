<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Request;

final class RequestAttributeValueSame extends Constraint
{
    private $name;
    private $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return sprintf('has attribute "%s" with value "%s"', $this->name, $this->value);
    }

    /**
     * @param Request $request
     *
     * {@inheritdoc}
     */
    protected function matches($request): bool
    {
        return $this->value === $request->attributes->get($this->name);
    }

    /**
     * @param Request $request
     *
     * {@inheritdoc}
     */
    protected function failureDescription($request): string
    {
        return 'the Request '.$this->toString();
    }
}
