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

namespace League\Uri\UriTemplate;

use League\Uri\Exceptions\SyntaxError;

use function preg_match;

/**
 * @internal The class exposes the internal representation of a Var Specifier
 * @link https://www.rfc-editor.org/rfc/rfc6570#section-2.3
 */
final class VarSpecifier
{
    /**
     * Variables specification regular expression pattern.
     *
     * @link https://tools.ietf.org/html/rfc6570#section-2.3
     */
    private const REGEXP_VARSPEC = '/^
        (?<name>
            (?:[A-Za-z0-9_\[\]]|%[0-9a-fA-F]{2})+
            (?:\.(?:[A-Za-z0-9_\[\]]|%[0-9a-fA-F]{2})+)*
        )
        (?<modifier>\:(?<position>\d+)|\*)?
    $/x';

    private const MODIFIER_POSITION_MAX_POSITION = 10_000;

    private function __construct(
        public readonly string $name,
        public readonly string $modifier,
        public readonly int $position
    ) {
    }

    public static function new(string $specification): self
    {
        1 === preg_match(self::REGEXP_VARSPEC, $specification, $parsed) || throw new SyntaxError('The variable specification "'.$specification.'" is invalid.');
        $properties = ['name' => $parsed['name'], 'modifier' => $parsed['modifier'] ?? '', 'position' => $parsed['position'] ?? ''];

        if ('' !== $properties['position']) {
            1 === preg_match('/^(?:0|[1-9]\d*)$/', $properties['position']) || throw new SyntaxError('The variable specification "'.$specification.'" is invalid.');
            $properties['position'] = (int) $properties['position'];
            0 !== $properties['position'] || throw new SyntaxError('The variable specification "'.$specification.'" is invalid the position modifier must be greater than 0.');

            $properties['modifier'] = ':';
        }

        if ('' === $properties['position']) {
            $properties['position'] = 0;
        }

        self::MODIFIER_POSITION_MAX_POSITION > $properties['position'] || throw new SyntaxError('The variable specification "'.$specification.'" is invalid the position modifier must be lower than 10000.');

        return new self($properties['name'], $properties['modifier'], $properties['position']);
    }

    public function toString(): string
    {
        return $this->name.$this->modifier.match (true) {
            0 < $this->position => $this->position,
            default => '',
        };
    }
}
