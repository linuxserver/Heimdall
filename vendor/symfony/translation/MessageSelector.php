<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

@trigger_error(sprintf('The "%s" class is deprecated since Symfony 4.2, use IdentityTranslator instead.', MessageSelector::class), E_USER_DEPRECATED);

use Symfony\Component\Translation\Exception\InvalidArgumentException;

/**
 * MessageSelector.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @deprecated since Symfony 4.2, use IdentityTranslator instead.
 */
class MessageSelector
{
    /**
     * Given a message with different plural translations separated by a
     * pipe (|), this method returns the correct portion of the message based
     * on the given number, locale and the pluralization rules in the message
     * itself.
     *
     * The message supports two different types of pluralization rules:
     *
     * interval: {0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples
     * indexed:  There is one apple|There are %count% apples
     *
     * The indexed solution can also contain labels (e.g. one: There is one apple).
     * This is purely for making the translations more clear - it does not
     * affect the functionality.
     *
     * The two methods can also be mixed:
     *     {0} There are no apples|one: There is one apple|more: There are %count% apples
     *
     * @param string    $message The message being translated
     * @param int|float $number  The number of items represented for the message
     * @param string    $locale  The locale to use for choosing
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function choose($message, $number, $locale)
    {
        $parts = array();
        if (preg_match('/^\|++$/', $message)) {
            $parts = explode('|', $message);
        } elseif (preg_match_all('/(?:\|\||[^\|])++/', $message, $matches)) {
            $parts = $matches[0];
        }

        $explicitRules = array();
        $standardRules = array();
        foreach ($parts as $part) {
            $part = trim(str_replace('||', '|', $part));

            if (preg_match('/^(?P<interval>'.Interval::getIntervalRegexp().')\s*(?P<message>.*?)$/xs', $part, $matches)) {
                $explicitRules[$matches['interval']] = $matches['message'];
            } elseif (preg_match('/^\w+\:\s*(.*?)$/', $part, $matches)) {
                $standardRules[] = $matches[1];
            } else {
                $standardRules[] = $part;
            }
        }

        // try to match an explicit rule, then fallback to the standard ones
        foreach ($explicitRules as $interval => $m) {
            if (Interval::test($number, $interval)) {
                return $m;
            }
        }

        $position = PluralizationRules::get($number, $locale);

        if (!isset($standardRules[$position])) {
            // when there's exactly one rule given, and that rule is a standard
            // rule, use this rule
            if (1 === \count($parts) && isset($standardRules[0])) {
                return $standardRules[0];
            }

            throw new InvalidArgumentException(sprintf('Unable to choose a translation for "%s" with locale "%s" for value "%d". Double check that this translation has the correct plural options (e.g. "There is one apple|There are %%count%% apples").', $message, $locale, $number));
        }

        return $standardRules[$position];
    }
}
