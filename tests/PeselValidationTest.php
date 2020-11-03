<?php

declare(strict_types=1);

use Pesel\Pesel;
use Pesel\PeselValidator;
use PHPUnit\Framework\TestCase;

class PeselValidationTest extends TestCase
{

    /**
     * @dataProvider birthDateDataProvider
     */
    public function testHasBirthDateReturnsCorrectValue(Pesel $pesel, string $birthDate, bool $isCorrect): void
    {
        $actual = $pesel->hasBirthDate(DateTime::createFromFormat('Y-m-d', $birthDate));

        $actualStr = $actual ? 'true' : 'false';
        $isCorrectStr = $isCorrect ? 'true' : 'false';

        $this->assertEquals(
            $isCorrect,
            $actual,
            "Invalid gender. Got $actualStr, expected $isCorrectStr for number $pesel"
        );
    }

    /**
     * @dataProvider genderDataProvider
     */
    public function testHasGenderReturnsCorrectValue(Pesel $pesel, int $gender, bool $isCorrect): void
    {
        $actual = $pesel->hasGender($gender);

        $actualStr = $actual ? 'true' : 'false';
        $isCorrectStr = $isCorrect ? 'true' : 'false';

        $this->assertEquals(
            $isCorrect,
            $actual,
            "Invalid gender. Got $actualStr, expected $isCorrectStr for number $pesel and gender $gender"
        );
    }

    /**
     * @dataProvider genderInputsDataProvider
     */
    public function testHasGenderThrowsExceptionWhenGenderInputIsInvalid(int $gender, bool $isValid)
    {
        $pesel = new Pesel("00010100008");

        if ($isValid == false) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('Podano płeć w niepoprawnym formacie');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $pesel->hasGender($gender);
    }

    public function birthDateDataProvider(): array
    {
        return [
            [new Pesel('00810100002'), '1800-01-01', true],
            [new Pesel('50910100000'), '1850-11-01', true],
            [new Pesel('00010100008'), '1900-01-01', true],
            [new Pesel('50110100006'), '1950-11-01', true],
            [new Pesel('00210100004'), '2000-01-01', true],
            [new Pesel('50310100002'), '2050-11-01', true],
            [new Pesel('00410100000'), '2100-01-01', true],
            [new Pesel('50510100008'), '2150-11-01', true],
            [new Pesel('00610100006'), '2200-01-01', true],
            [new Pesel('50710100004'), '2250-11-01', true],

            [new Pesel('00810100002'), '1802-01-01', false],
            [new Pesel('50910100000'), '1853-11-01', false],
            [new Pesel('00010100008'), '1904-01-01', false],
            [new Pesel('50110100006'), '1955-11-01', false],
            [new Pesel('00210100004'), '2006-01-01', false],
            [new Pesel('50310100002'), '2057-11-01', false],
            [new Pesel('00410100000'), '2108-01-01', false],
            [new Pesel('50510100008'), '2159-11-01', false],
            [new Pesel('00610100006'), '2210-01-01', false],
            [new Pesel('50710100004'), '2260-11-01', false],
        ];
    }

    public function genderDataProvider(): array
    {
        return [
            [new Pesel('83082317338'), Pesel::GENDER_MALE, true],
            [new Pesel('83082317338'), Pesel::GENDER_MALE, true],
            [new Pesel('62022216858'), Pesel::GENDER_MALE, true],
            [new Pesel('80052117613'), Pesel::GENDER_MALE, true],
            [new Pesel('40041212350'), Pesel::GENDER_MALE, true],
            [new Pesel('93080611761'), Pesel::GENDER_FEMALE, true],
            [new Pesel('08232314148'), Pesel::GENDER_FEMALE, true],
            [new Pesel('97100911864'), Pesel::GENDER_FEMALE, true],
            [new Pesel('45021009506'), Pesel::GENDER_FEMALE, true],
            [new Pesel('50011703922'), Pesel::GENDER_FEMALE, true],

            [new Pesel('83082317338'), Pesel::GENDER_FEMALE, false],
            [new Pesel('83082317338'), Pesel::GENDER_FEMALE, false],
            [new Pesel('62022216858'), Pesel::GENDER_FEMALE, false],
            [new Pesel('80052117613'), Pesel::GENDER_FEMALE, false],
            [new Pesel('40041212350'), Pesel::GENDER_FEMALE, false],
            [new Pesel('93080611761'), Pesel::GENDER_MALE, false],
            [new Pesel('08232314148'), Pesel::GENDER_MALE, false],
            [new Pesel('97100911864'), Pesel::GENDER_MALE, false],
            [new Pesel('45021009506'), Pesel::GENDER_MALE, false],
            [new Pesel('50011703922'), Pesel::GENDER_MALE, false],
        ];
    }

    public function genderInputsDataProvider(): array
    {
        return [
            [Pesel::GENDER_MALE, true],
            [Pesel::GENDER_FEMALE, true],
            [0, true],
            [1, true],
            [2, false],
        ];
    }
}
