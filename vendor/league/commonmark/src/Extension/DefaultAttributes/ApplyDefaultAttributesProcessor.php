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

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Node\Node;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use League\Config\Exception\InvalidConfigurationException;

final class ApplyDefaultAttributesProcessor implements ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    /**
     * Configured attributes with every callback already resolved to a `Closure`, keyed by node FQCN.
     *
     * @var array<string, array<string, mixed>>|null
     */
    private ?array $resolved = null;

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        if ($this->resolved === null) {
            $this->resolved = $this->resolveConfiguredAttributes();
        }

        $map = $this->resolved;

        // Don't bother iterating if no default attributes are configured
        if (! $map) {
            return;
        }

        foreach ($event->getDocument()->iterator() as $node) {
            // Check to see if any default attributes were defined
            if (($attributesToApply = $map[\get_class($node)] ?? []) === []) {
                continue;
            }

            $newAttributes = [];
            foreach ($attributesToApply as $name => $value) {
                if ($value instanceof \Closure) {
                    $value = $value($node);
                    // Callables are allowed to return `null` indicating that no changes should be made
                    if ($value !== null) {
                        $newAttributes[$name] = $value;
                    }
                } else {
                    $newAttributes[$name] = $value;
                }
            }

            // Merge these attributes into the node
            if (\count($newAttributes) > 0) {
                $node->data->set('attributes', AttributesHelper::mergeAttributes($node, $newAttributes));
            }
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function resolveConfiguredAttributes(): array
    {
        /** @var array<string, array<string, mixed>> $map */
        $map    = $this->config->get('default_attributes/attributes');
        $strict = (bool) $this->config->get('default_attributes/strict_callables');

        $resolved = [];
        foreach ($map as $class => $attributes) {
            foreach ($attributes as $name => $value) {
                $resolved[$class][$name] = self::resolveValue($class, $name, $value, $strict);
            }
        }

        return $resolved;
    }

    /**
     * @param mixed $value
     *
     * @return mixed A `Closure` if the value is a callback, otherwise the literal attribute value
     */
    private static function resolveValue(string $class, string $name, $value, bool $strict)
    {
        if ($value instanceof \Closure) {
            return $value;
        }

        if (\is_string($value) || \is_array($value)) {
            // Strings and arrays are how literal attribute values are written, and plenty of them also
            // name a real function - PHP has a global `link()`, for example
            if ($strict) {
                return $value;
            }

            if (! \is_callable($value)) {
                return $value;
            }

            return self::wrapCallable($class, $name, $value, \Closure::fromCallable($value));
        }

        return \is_callable($value) ? \Closure::fromCallable($value) : $value;
    }

    /**
     * Invoke the callable, replacing a failure with one which names the configuration responsible.
     *
     * A value like `'link'` is otherwise invoked and fails deep inside whichever function it collided
     * with, reporting an argument count the user never wrote.
     *
     * @param string|mixed[] $value The callable as it was written in the configuration
     */
    private static function wrapCallable(string $class, string $name, $value, \Closure $callback): \Closure
    {
        return static function (Node $node) use ($callback, $class, $name, $value) {
            try {
                return $callback($node);
            } catch (\TypeError $e) {
                throw new InvalidConfigurationException(\sprintf(
                    'The "%s" attribute configured for %s is %s, which PHP also treats as a callable, so it was '
                    . 'called with the node and failed: %s. If it was meant to be a literal attribute value instead, '
                    . 'set "default_attributes/strict_callables" to true.',
                    $name,
                    $class,
                    \is_string($value) ? \sprintf('the string "%s"', $value) : 'an array',
                    $e->getMessage()
                ), 0, $e);
            }
        };
    }
}
