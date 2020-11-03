<?php

declare(strict_types=1);

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
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class Pesel
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
     * @throws InvalidArgumentException when PESEL number is invalid.
     */
    public function __construct(string $number, array $errorMessages = [])
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
     * @return static
     * @throws InvalidArgumentException when PESEL number is invalid.
     */
    public static function create(string $number)
    {
        return new static($number);
    }

    /**
     * Check if provided number is valid.
     *
     * @param string $number
     */
    public static function isValid(string $number): bool
    {
        try {
            new static($number);

            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Get birth date encoded in the number.
     */
    public function getBirthDate(): DateTimeImmutable
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

        $month = str_pad((string) ($month % 20), 2, '0', STR_PAD_LEFT);

        return DateTimeImmutable::createFromFormat('Y-m-d', "$year-$month-$day");
    }

    /**
     * Check if PESEL number contains provided birth date.
     */
    public function hasBirthDate(DateTimeInterface $birthDate): bool
    {
        return $this->getBirthDate() == $birthDate;
    }

    /**
     * Get gender encoded in the number.
     */
    public function getGender(): int
    {
        return $this->number[static::$genderDigit] % 2;
    }

    /**
     * Check if PESEL number contains provided gender.
     *
     * @param int $gender Pesel::GENDER_FEMALE or Pesel::GENDER_MALE
     * @throws InvalidArgumentException when provided gender is neither Pesel::GENDER_FEMALE nor PESEL::GENDER_MALE
     */
    public function hasGender(int $gender): bool
    {
        static::validateGenderInput($gender);

        return $this->getGender() == $gender;
    }

    public function __toString(): string
    {
        return $this->number;
    }

    /**
     * @throws InvalidArgumentException when number has invalid length
     */
    protected function validateLength(): void
    {
        if (strlen($this->number) !== self::$peselLength) {
            throw new InvalidArgumentException($this->errorMessages['invalidLength']);
        }
    }

    /**
     * @throws InvalidArgumentException when number contains non-digits
     */
    protected function validateDigitsOnly(): void
    {
        if (ctype_digit($this->number) === false) {
            throw new InvalidArgumentException($this->errorMessages['invalidCharacters']);
        }
    }

    /**
     * @throws InvalidArgumentException on invalid checksum
     */
    protected function validateChecksum(): void
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
     * @param int $gender Pesel::GENDER_FEMALE or Pesel::GENDER_MALE
     * @throws InvalidArgumentException when provided gender is not Pesel::GENDER_FEMALE nor PESEL::GENDER_MALE
     */
    protected static function validateGenderInput(int $gender): void
    {
        if ($gender !== static::GENDER_FEMALE && $gender !== static::GENDER_MALE) {
            throw new InvalidArgumentException('Podano płeć w niepoprawnym formacie');
        }
    }
}
