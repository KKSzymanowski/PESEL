<?php

namespace Pesel\Traits;

use Carbon\Carbon;

trait BirthDateValidation
{

    /**
     * Check if the birth date encoded in PESEL number
     * is the same as provided
     *
     *
     * @param Carbon $birthDate
     * @return bool
     */
    public function hasBirthDate(Carbon $birthDate)
    {
        return $this->getBirthDateFromNumber() == $birthDate->format('Y-m-d');
    }

    /**
     * Alias for hasDateOfBirth
     *
     * @param Carbon $dateOfBirth
     * @return bool
     */
    public function hasDateOfBirth(Carbon $dateOfBirth)
    {
        return $this->hasBirthDate($dateOfBirth);
    }

    protected function getBirthDateFromNumber()
    {
        $year = substr($this->number, 0, 2);
        $month = substr($this->number, 2, 2);
        $day = substr($this->number, 4, 2);

        // 0 - 9
        $century = substr($this->number, 2, 1);
        // 2,3,4,5,6,7,8,9,10,11
        $century += 2;
        // 2,3,4,5,6,7,8,9,0,1
        $century %= 10;
        // 1,1,2,2,3,3,4,4,0,0
        $century = round($century / 2, 0, PHP_ROUND_HALF_DOWN);
        // 19,19,20,20,21,21,22,22,18,18
        $century += 18;

        $year = $century . $year;

        $month = str_pad($month % 20, 2, '0', STR_PAD_LEFT);

        return "${year}-${month}-${day}";
    }
}