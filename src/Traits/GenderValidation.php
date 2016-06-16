<?php

namespace Pesel\Traits;

use InvalidArgumentException;

trait GenderValidation
{
    /**
     * Check if gender encoded in PESEL
     * number matches provided gender
     *
     * @param $gender
     * @return bool
     */
    public function hasGender($gender)
    {
        $gender = $this->normalizeGender($gender);

        return $this->number[static::GENDER_DIGIT] % 2 == $gender;
    }

    protected function normalizeGender($gender)
    {
        if (! array_key_exists($gender, static::GENDERS)) {
            throw new InvalidArgumentException("Invalid gender: " . $gender);
        }

        return static::GENDERS[$gender];
    }
}