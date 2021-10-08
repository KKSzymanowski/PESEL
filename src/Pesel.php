<?php

/*
 * KKSzymanowski/PESEL
 * Walidacja numeru PESEL
 *
 * @package KKSzymanowski/PESEL
 * @author Kuba Szymanowski <kuba.szymanowski@inf24.pl>
 * @link https://github.com/kkszymanowski/pesel
 * @license MIT
 */

namespace Pesel;

use DateTime;
use InvalidArgumentException;
use Pesel\Exceptions\InvalidBirthDateException;
use Pesel\Exceptions\InvalidCharactersException;
use Pesel\Exceptions\InvalidChecksumException;
use Pesel\Exceptions\InvalidGenderInputException;
use Pesel\Exceptions\InvalidLengthException;

class Pesel
{
    const GENDER_FEMALE = 0;

    const GENDER_MALE = 1;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var array
     */
    protected $errorMessages = [
        'invalidLength'     => 'Nieprawidłowa długość numeru PESEL.',
        'invalidCharacters' => 'Numer PESEL może zawierać tylko cyfry.',
        'invalidChecksum'   => 'Numer PESEL posiada niepoprawną sumę kontrolną.',
        'invalidBirthDate'  => 'Numer PESEL zawiera nieprawidłową datę urodzenia.',
    ];

    /**
     * @var int
     */
    protected static $genderDigit = 9;

    /**
     * @var int
     */
    protected static $peselLength = 11;

    /**
     * Weights assigned to each digit.
     *
     * Used when calculating checksum.
     *
     * @var array
     */
    protected static $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];

    /**
     * @param string $number
     * @param array  $errorMessages Deprecated, please catch a specific exception extending PeselValidationException
     *
     * @throws InvalidArgumentException when PESEL number is invalid.
     */
    public function __construct($number, $errorMessages = [])
    {
        $this->errorMessages = array_merge($this->errorMessages, $errorMessages);

        $this->number = $number;

        $this->validateLength();

        $this->validateDigitsOnly();

        $this->validateChecksum();

        $this->validateBirthDateDigits();
    }

    /**
     * A glorified constructor.
     *
     * @param string $number
     *
     * @throws InvalidArgumentException when PESEL number is invalid.
     *
     * @return static
     */
    public static function create($number)
    {
        return new static($number);
    }

    /**
     * Check if provided number is valid.
     *
     * @param string $number
     *
     * @return bool
     */
    public static function isValid($number)
    {
        try {
            new static($number);

            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get birth date encoded in the number.
     *
     * @return DateTime
     */
    public function getBirthDate()
    {
        [$year, $month, $day] = $this->getYearMonthDate();

        return DateTime::createFromFormat('Y-m-d H:i:s', "$year-$month-$day 00:00:00");
    }

    /**
     * Check if PESEL number contains provided birth date.
     *
     * @param DateTime $birthDate
     *
     * @return bool
     */
    public function hasBirthDate(DateTime $birthDate)
    {
        return $this->getBirthDate() == (clone $birthDate)->setTime(0, 0, 0);
    }

    /**
     * Get gender encoded in the number.
     *
     * @return int
     */
    public function getGender()
    {
        return $this->number[static::$genderDigit] % 2;
    }

    /**
     * Check if PESEL number contains provided gender.
     *
     * @param int $gender Pesel::GENDER_FEMALE or Pesel::GENDER_MALE
     *
     * @throws InvalidArgumentException when provided gender is neither Pesel::GENDER_FEMALE nor PESEL::GENDER_MALE
     *
     * @return bool
     */
    public function hasGender($gender)
    {
        static::validateGenderInput($gender);

        return $this->getGender() == $gender;
    }

    /**
     * String representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->number;
    }

    /**
     * @throws InvalidArgumentException when number has invalid length
     */
    protected function validateLength()
    {
        if (strlen($this->number) !== self::$peselLength) {
            throw new InvalidLengthException($this->errorMessages['invalidLength']);
        }
    }

    /**
     * @throws InvalidArgumentException when number contains non-digits
     */
    protected function validateDigitsOnly()
    {
        if (ctype_digit($this->number) === false) {
            throw new InvalidCharactersException($this->errorMessages['invalidCharacters']);
        }
    }

    /**
     * @throws InvalidArgumentException on invalid checksum
     */
    protected function validateChecksum()
    {
        $digitArray = str_split($this->number);

        $checksum = array_reduce(array_keys($digitArray), function ($carry, $index) use ($digitArray) {
            return $carry + static::$weights[$index] * $digitArray[$index];
        });

        if ($checksum % 10 !== 0) {
            throw new InvalidChecksumException($this->errorMessages['invalidChecksum']);
        }
    }

    /**
     * @throws InvalidArgumentException on invalid birth date digits
     */
    protected function validateBirthDateDigits()
    {
        [$year, $month, $day] = $this->getYearMonthDate();

        if ($year < 1800 || $year > 2299) {
            throw new InvalidBirthDateException($this->errorMessages['invalidBirthDate']);
        }

        if ($month < 1 || $month > 12) {
            throw new InvalidBirthDateException($this->errorMessages['invalidBirthDate']);
        }

        if ($day < 1 || $day > cal_days_in_month(CAL_GREGORIAN, $month, $year)) {
            throw new InvalidBirthDateException($this->errorMessages['invalidBirthDate']);
        }
    }

    /**
     * Check if provided gender matches accepted format.
     *
     * @param int $gender Pesel::GENDER_FEMALE or Pesel::GENDER_MALE
     *
     * @throws InvalidArgumentException when provided gender is not Pesel::GENDER_FEMALE nor PESEL::GENDER_MALE
     */
    protected static function validateGenderInput($gender)
    {
        if ($gender !== static::GENDER_FEMALE &&
            $gender !== static::GENDER_MALE &&
            $gender !== (string) self::GENDER_FEMALE &&
            $gender !== (string) self::GENDER_MALE
        ) {
            throw new InvalidGenderInputException('Podano płeć w niepoprawnym formacie');
        }
    }

    protected function getYearMonthDate()
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

        $year = $century.$year;

        $month = str_pad($month % 20, 2, '0', STR_PAD_LEFT);

        return [$year, $month, $day];
    }
}
