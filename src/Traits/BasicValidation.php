<?php

namespace Pesel\Traits;

trait BasicValidation
{
    /**
     * Weights assigned to each digit.
     *
     * Used when calculating checksum.
     *
     * @var array
     */
    protected static $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

    /**
     * Check if PESEL number is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        if (! $this->hasValidLength())
            return false;

        if (! $this->containsOnlyDigits())
            return false;

        if (! $this->hasValidChecksum())
            return false;

        return true;
    }

    /**
     * @return bool
     */
    public function hasValidLength()
    {
        return strlen($this->number) == self::PESEL_LENGTH;
    }

    /**
     * @return bool
     */
    public function containsOnlyDigits()
    {
        return ctype_digit($this->number);
    }

    /**
     * @return bool
     */
    public function hasValidChecksum()
    {
        $digitArray = str_split($this->number);

        $checksum = array_reduce(array_keys($digitArray), function ($carry, $index) use ($digitArray) {
            return $carry + static::$weights[$index] * $digitArray[$index];
        });

        return $checksum % 10 == 0;
    }
}