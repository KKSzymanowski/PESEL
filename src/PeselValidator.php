<?php

namespace Pesel;

use DateTime;
use InvalidArgumentException;

class PeselValidator
{
    /**
     * Check if PESEL number contains provided birth date.
     *
     * @param Pesel $pesel
     * @param DateTime $birthDate
     * @return bool
     */
    public static function hasBirthDate(Pesel $pesel, DateTime $birthDate)
    {
        return $pesel->getBirthDate()->format('Y-m-d') == $birthDate->format('Y-m-d');
    }

    /**
     * Alias for PeselValidator::hasBirthDate.
     *
     * @param Pesel $pesel
     * @param DateTime $birthDate
     * @return bool
     */
    public static function hasDateOfBirth(Pesel $pesel, DateTime $birthDate)
    {
        return static::hasBirthDate($pesel, $birthDate);
    }

    /**
     * Check if PESEL number contains provided gender.
     *
     * Accepted formats are
     * Pesel::GENDER_FEMALE = 0
     * and
     * Pesel::GENDER_MALE = 1
     *
     * @param Pesel $pesel
     * @param int $gender
     * @return bool
     */
    public static function hasGender(Pesel $pesel, $gender)
    {
        static::validateGenderInput($gender);

        return $pesel->getGender() == $gender;
    }

    /**
     * Check if provided gender matches accepted format.
     *
     * @param int $gender
     */
    protected static function validateGenderInput($gender)
    {
        if ($gender !== Pesel::GENDER_FEMALE &&
            $gender !== Pesel::GENDER_MALE &&
            $gender !== (string) Pesel::GENDER_FEMALE &&
            $gender !== (string) Pesel::GENDER_MALE
        ) {
            throw new InvalidArgumentException('Podano płeć w niepoprawnym formacie');
        }
    }
}
