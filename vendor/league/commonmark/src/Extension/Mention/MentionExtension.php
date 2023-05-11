<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Exception\InvalidOptionException;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;

final class MentionExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $mentions = $environment->getConfig('mentions', []);
        foreach ($mentions as $name => $mention) {
            if (\array_key_exists('symbol', $mention)) {
                @\trigger_error('The "mentions/*/symbol" configuration option is deprecated in league/commonmark 1.6; rename "symbol" to "prefix" for compatibility with 2.0', \E_USER_DEPRECATED);
                $mention['prefix'] = $mention['symbol'];
            }

            if (\array_key_exists('pattern', $mention)) {
                // v2.0 does not allow full regex patterns passed into the configuration
                if (!self::isAValidPartialRegex($mention['pattern'])) {
                    throw new InvalidOptionException(\sprintf('Option "mentions/%s/pattern" must not include starting/ending delimiters (like "/")', $name));
                }

                $mention['pattern'] = '/' . $mention['pattern'] . '/i';
            } elseif (\array_key_exists('regex', $mention)) {
                @\trigger_error('The "mentions/*/regex" configuration option is deprecated in league/commonmark 1.6; rename "regex" to "pattern" for compatibility with 2.0', \E_USER_DEPRECATED);
                $mention['pattern'] = $mention['regex'];
            }

            foreach (['prefix', 'pattern', 'generator'] as $key) {
                if (empty($mention[$key])) {
                    throw new \RuntimeException("Missing \"$key\" from MentionParser configuration");
                }
            }
            if ($mention['generator'] instanceof MentionGeneratorInterface) {
                $environment->addInlineParser(new MentionParser($mention['prefix'], $mention['pattern'], $mention['generator']));
            } elseif (is_string($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithStringTemplate($mention['prefix'], $mention['pattern'], $mention['generator']));
            } elseif (is_callable($mention['generator'])) {
                $environment->addInlineParser(MentionParser::createWithCallback($mention['prefix'], $mention['pattern'], $mention['generator']));
            } else {
                throw new \RuntimeException(sprintf('The "generator" provided for the MentionParser configuration must be a string template, callable, or an object that implements %s.', MentionGeneratorInterface::class));
            }
        }
    }

    private static function isAValidPartialRegex(string $regex): bool
    {
        $regex = '/' . $regex . '/i';

        return @\preg_match($regex, '') !== false;
    }
}
