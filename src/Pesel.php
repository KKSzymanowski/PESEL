<?php

namespace Pesel;

use DateTime;
use InvalidArgumentException;

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
        'invalidLength' => 'Nieprawidłowa długość numeru PESEL.',
        'invalidCharacters' => 'Numer PESEL może zawierać tylko cyfry.',
        'invalidChecksum' => 'Numer PESEL posiada niepoprawną sumę kontrolną.',
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
     * Throws InvalidArgumentException when PESEL number is invalid.
     *
     * @param string $number
     * @param array $errorMessages
     * @throws InvalidArgumentException
     */
    public function __construct($number, $errorMessages = [])
    {
        $this->errorMessages = array_merge($this->errorMessages, $errorMessages);

        $this->number = $number;

        $this->validateLength();

        $this->validateDigitsOnly();

        $this->validateChecksum();
    }

    /**
     * A glorified constructor.
     *
     * Throws InvalidArgumentException when PESEL number is invalid.
     *
     * @param string $number
     * @return static
     * @throws InvalidArgumentException
     */
    public static function create($number)
    {
        return new static($number);
    }

    /**
     * Check if provided number is valid.
     *
     * @param string $number
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

        return DateTime::createFromFormat('Y-m-d', "$year-$month-$day");
    }

    /**
     * Check if PESEL number contains provided birth date.
     *
     * @param DateTime $birthDate
     * @return bool
     */
    public function hasBirthDate(DateTime $birthDate)
    {
        return $this->getBirthDate() == $birthDate;
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
     * @return bool
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    protected function validateLength()
    {
        if (strlen($this->number) !== self::$peselLength) {
            throw new InvalidArgumentException($this->errorMessages['invalidLength']);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function validateDigitsOnly()
    {
        if (ctype_digit($this->number) === false) {
            throw new InvalidArgumentException($this->errorMessages['invalidCharacters']);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function validateChecksum()
    {
        $digitArray = str_split($this->number);

        $checksum = array_reduce(array_keys($digitArray), function ($carry, $index) use ($digitArray) {
            return $carry + static::$weights[$index] * $digitArray[$index];
        });

        if ($checksum % 10 !== 0) {
            throw new InvalidArgumentException($this->errorMessages['invalidChecksum']);
        }
    }

    /**
     * Check if provided gender matches accepted format.
     *
     * @param int $gender
     * @throws InvalidArgumentException
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
