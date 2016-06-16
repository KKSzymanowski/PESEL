<?php

namespace Pesel\Traits;

use InvalidArgumentException;

trait GenderValidation
{
    /**
     * All possible gender definitions
     *
     * @var array
     */
    public static $genders = [
        'K' => self::GENDER_FEMALE,
        'k' => self::GENDER_FEMALE,
        'W' => self::GENDER_FEMALE,
        'w' => self::GENDER_FEMALE,
        'F' => self::GENDER_FEMALE,
        'f' => self::GENDER_FEMALE,

        'M' => self::GENDER_MALE,
        'm' => self::GENDER_MALE,

        self::GENDER_FEMALE => self::GENDER_FEMALE,
        self::GENDER_MALE => self::GENDER_MALE,
    ];

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
        if (! array_key_exists($gender, static::$genders)) {
            throw new InvalidArgumentException("Invalid gender: " . $gender);
        }

        return static::$genders[$gender];
    }
}