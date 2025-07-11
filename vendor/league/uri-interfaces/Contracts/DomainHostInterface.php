<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Contracts;

use Countable;
use Iterator;
use IteratorAggregate;
use League\Uri\Exceptions\SyntaxError;
use Stringable;

/**
 * @extends IteratorAggregate<string>
 */
interface DomainHostInterface extends Countable, HostInterface, IteratorAggregate
{
    /**
     * Returns the labels total number.
     */
    public function count(): int;

    /**
     * Iterate over the Domain labels.
     *
     * @return Iterator<string>
     */
    public function getIterator(): Iterator;

    /**
     * Retrieves a single host label.
     *
     * If the label offset has not been set, returns the null value.
     */
    public function get(int $offset): ?string;

    /**
     * Returns the associated key for a specific label or all the keys.
     *
     * @return int[]
     */
    public function keys(?string $label = null): array;

    /**
     * Tells whether the domain is absolute.
     */
    public function isAbsolute(): bool;

    /**
     * Prepends a label to the host.
     */
    public function prepend(Stringable|string $label): self;

    /**
     * Appends a label to the host.
     */
    public function append(Stringable|string $label): self;

    /**
     * Extracts a slice of $length elements starting at position $offset from the host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the selected slice.
     *
     * If $length is null it returns all elements from $offset to the end of the Domain.
     */
    public function slice(int $offset, ?int $length = null): self;

    /**
     * Returns an instance with its Root label.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     */
    public function withRootLabel(): self;

    /**
     * Returns an instance without its Root label.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     */
    public function withoutRootLabel(): self;

    /**
     * Returns an instance with the modified label.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the new label
     *
     * If $key is non-negative, the added label will be the label at $key position from the start.
     * If $key is negative, the added label will be the label at $key position from the end.
     *
     * @throws SyntaxError If the key is invalid
     */
    public function withLabel(int $key, Stringable|string $label): self;

    /**
     * Returns an instance without the specified label.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     *
     * If $key is non-negative, the removed label will be the label at $key position from the start.
     * If $key is negative, the removed label will be the label at $key position from the end.
     *
     * @throws SyntaxError If the key is invalid
     */
    public function withoutLabel(int ...$keys): self;
}
