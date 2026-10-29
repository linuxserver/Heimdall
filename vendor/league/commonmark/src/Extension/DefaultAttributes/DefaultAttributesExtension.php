<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\DefaultAttributes;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class DefaultAttributesExtension implements ConfigurableExtensionInterface
{
    /**
     * Keys belonging to this extension's own settings rather than to a node class
     */
    private const RESERVED_KEYS = ['attributes' => 0, 'strict_callables' => 0];

    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('default_attributes', Expect::structure([
            'attributes' => Expect::arrayOf(
                Expect::arrayOf(
                    Expect::type('string|string[]|bool|callable'), // attribute value(s)
                    'string' // attribute name
                ),
                'string' // node FQCN
            )->default([]),
            // @deprecated This option will be removed in 3.0, when only closures and invokable objects
            // will ever be treated as callbacks.
            'strict_callables' => Expect::bool()->default(true),
        ])->before(static function ($value) {
            if (! \is_array($value)) {
                return $value;
            }

            $canonical = \array_intersect_key($value, self::RESERVED_KEYS);
            $legacy    = \array_diff_key($value, self::RESERVED_KEYS);

            if ($legacy === []) {
                return $value;
            }

            // Nothing can be merged into an `attributes` which isn't an array; hand it to the schema
            // as-is so it reports the same validation error it would without the node classes present
            if (isset($canonical['attributes']) && ! \is_array($canonical['attributes'])) {
                return $canonical;
            }

            $canonical['attributes'] = \array_merge($legacy, $canonical['attributes'] ?? []);

            // The flat shape predates this option, so it stays on the legacy callable handling
            // unless it opts in explicitly.
            if (! \array_key_exists('strict_callables', $canonical)) {
                $canonical['strict_callables'] = false;
            }

            return $canonical;
        }));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, [new ApplyDefaultAttributesProcessor(), 'onDocumentParsed']);
    }
}
