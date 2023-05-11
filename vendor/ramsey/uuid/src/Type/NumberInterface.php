<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Type;

/**
 * NumberInterface ensures consistency in numeric values returned by ramsey/uuid
 *
 * @psalm-immutable
 */
interface NumberInterface extends TypeInterface
{
    /**
     * Returns true if this number is less than zero
     */
    public function isNegative(): bool;
}
