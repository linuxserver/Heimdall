<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

final class GenericObjectType extends Type
{
    /**
     * @var bool
     */
    private $allowsNull;

    public function __construct(bool $nullable)
    {
        $this->allowsNull = $nullable;
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if (!$other instanceof ObjectType) {
            return false;
        }

        return true;
    }

    public function name(): string
    {
        return 'object';
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function isGenericObject(): bool
    {
        return true;
    }
}
