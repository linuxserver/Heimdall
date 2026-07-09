<?php

/**
 * File for ComparisonTrait.
 * @package Phrity > Comparison
 */

namespace Phrity\Comparison;

/**
 * Trait that enables comparison methods.
 */
trait ComparisonTrait
{
    /**
     * If $this is equal to $that.
     * @param  mixed $that            The instance to compare with
     * @return boolean                True if $this is equal to $that
     * @throws IncomparableException  Thrown if $this can not be compared with $that
     */
    public function equals(mixed $that): bool
    {
        return $this->compare($that) == 0;
    }

    /**
     * If $this is greater than $that.
     * @param  mixed $that            The instance to compare with
     * @return boolean                True if $this is greater than $that
     * @throws IncomparableException  Thrown if $this can not be compared with $that
     */
    public function greaterThan(mixed $that): bool
    {
        return $this->compare($that) > 0;
    }

    /**
     * If $this is greater than or equal to $that.
     * @param  mixed $that            The instance to compare with
     * @return boolean                True if $this is greater than or equal to $that
     * @throws IncomparableException  Thrown if $this can not be compared with $that
     */
    public function greaterThanOrEqual(mixed $that): bool
    {
        return $this->compare($that) >= 0;
    }

    /**
     * If $this is less than $that.
     * @param  mixed $that            The instance to compare with
     * @return boolean                True if $this is less than $that
     * @throws IncomparableException  Thrown if $this can not be compared with $that
     */
    public function lessThan(mixed $that): bool
    {
        return $this->compare($that) < 0;
    }

    /**
     * If $this is less than or equal to $this.
     * @param  mixed $that            The instance to compare with
     * @return boolean                True if $this is less than or equal to $this
     * @throws IncomparableException  Thrown if $this can not be compared with $that
     */
    public function lessThanOrEqual(mixed $that): bool
    {
        return $this->compare($that) <= 0;
    }
}
