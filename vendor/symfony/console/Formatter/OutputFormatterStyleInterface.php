<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Formatter;

/**
 * Formatter style interface for defining styles.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface OutputFormatterStyleInterface
{
    /**
     * Sets style foreground color.
     */
    public function setForeground(string $color = null);

    /**
     * Sets style background color.
     */
    public function setBackground(string $color = null);

    /**
     * Sets some specific style option.
     */
    public function setOption(string $option);

    /**
     * Unsets some specific style option.
     */
    public function unsetOption(string $option);

    /**
     * Sets multiple style options at once.
     */
    public function setOptions(array $options);

    /**
     * Applies the style to a given text.
     *
     * @return string
     */
    public function apply(string $text);
}
