<?php

namespace Pesel;

use Carbon\Carbon;

interface PeselInterface
{
    /**
     * Which digit in PESEL number contains gender.
     */
    const GENDER_DIGIT = 9;

    const GENDER_FEMALE = 0;

    const GENDER_MALE = 1;

    const PESEL_LENGTH = 11;

    /**
     * Check if PESEL number is valid.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Check if the birth date encoded in PESEL number
     * is the same as provided.
     *
     * @param Carbon $dateOfBirth
     * @return bool
     */
    public function hasDateOfBirth(Carbon $dateOfBirth);

    /**
     * Alias for hasDateOfBirth.
     *
     * @param Carbon $birthDate
     * @return bool
     */
    public function hasBirthDate(Carbon $birthDate);

    /**
     * Check if gender encoded in PESEL
     * number matches provided gender.
     *
     * @param $gender
     * @return bool
     */
    public function hasGender($gender);

    /**
     * @return bool
     */
    public function hasValidLength();

    /**
     * @return bool
     */
    public function containsOnlyDigits();

    /**
     * @return bool
     */
    public function hasValidChecksum();
}
