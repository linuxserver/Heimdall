<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Question;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Represents a Question.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Question
{
    private $question;
    private $attempts;
    private $hidden = false;
    private $hiddenFallback = true;
    private $autocompleterValues;
    private $validator;
    private $default;
    private $normalizer;

    /**
     * @param string $question The question to ask to the user
     * @param mixed  $default  The default answer to return if the user enters nothing
     */
    public function __construct(string $question, $default = null)
    {
        $this->question = $question;
        $this->default = $default;
    }

    /**
     * Returns the question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Returns the default answer.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Returns whether the user response must be hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets whether the user response must be hidden or not.
     *
     * @param bool $hidden
     *
     * @return $this
     *
     * @throws LogicException In case the autocompleter is also used
     */
    public function setHidden($hidden)
    {
        if ($this->autocompleterValues) {
            throw new LogicException('A hidden question cannot use the autocompleter.');
        }

        $this->hidden = (bool) $hidden;

        return $this;
    }

    /**
     * In case the response can not be hidden, whether to fallback on non-hidden question or not.
     *
     * @return bool
     */
    public function isHiddenFallback()
    {
        return $this->hiddenFallback;
    }

    /**
     * Sets whether to fallback on non-hidden question if the response can not be hidden.
     *
     * @param bool $fallback
     *
     * @return $this
     */
    public function setHiddenFallback($fallback)
    {
        $this->hiddenFallback = (bool) $fallback;

        return $this;
    }

    /**
     * Gets values for the autocompleter.
     *
     * @return iterable|null
     */
    public function getAutocompleterValues()
    {
        return $this->autocompleterValues;
    }

    /**
     * Sets values for the autocompleter.
     *
     * @param iterable|null $values
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function setAutocompleterValues($values)
    {
        if (\is_array($values)) {
            $values = $this->isAssoc($values) ? array_merge(array_keys($values), array_values($values)) : array_values($values);
        }

        if (null !== $values && !\is_array($values) && !$values instanceof \Traversable) {
            throw new InvalidArgumentException('Autocompleter values can be either an array, "null" or a "Traversable" object.');
        }

        if ($this->hidden) {
            throw new LogicException('A hidden question cannot use the autocompleter.');
        }

        $this->autocompleterValues = $values;

        return $this;
    }

    /**
     * Sets a validator for the question.
     *
     * @param callable|null $validator
     *
     * @return $this
     */
    public function setValidator(callable $validator = null)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Gets the validator for the question.
     *
     * @return callable|null
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Sets the maximum number of attempts.
     *
     * Null means an unlimited number of attempts.
     *
     * @param int|null $attempts
     *
     * @return $this
     *
     * @throws InvalidArgumentException in case the number of attempts is invalid
     */
    public function setMaxAttempts($attempts)
    {
        if (null !== $attempts && $attempts < 1) {
            throw new InvalidArgumentException('Maximum number of attempts must be a positive value.');
        }

        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Gets the maximum number of attempts.
     *
     * Null means an unlimited number of attempts.
     *
     * @return int|null
     */
    public function getMaxAttempts()
    {
        return $this->attempts;
    }

    /**
     * Sets a normalizer for the response.
     *
     * The normalizer can be a callable (a string), a closure or a class implementing __invoke.
     *
     * @param callable $normalizer
     *
     * @return $this
     */
    public function setNormalizer(callable $normalizer)
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    /**
     * Gets the normalizer for the response.
     *
     * The normalizer can ba a callable (a string), a closure or a class implementing __invoke.
     *
     * @return callable
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    protected function isAssoc($array)
    {
        return (bool) \count(array_filter(array_keys($array), 'is_string'));
    }
}
