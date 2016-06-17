<?php

use Pesel\Pesel;
use Pesel\PeselValidator;

class PeselValidatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider birthDateDataProvider
     * @param Pesel $pesel
     * @param string $birthDate
     * @param bool $isCorrect
     */
    public function testHasBirthDateReturnsCorrectValue(Pesel $pesel, $birthDate, $isCorrect)
    {
        $actual = PeselValidator::hasBirthDate($pesel, DateTime::createFromFormat('Y-m-d', $birthDate));

        $this->assertEquals(
            $isCorrect,
            $actual,
            "Invalid gender. Got $actual, expected $isCorrect for number $pesel"
        );

        $actual = PeselValidator::hasDateOfBirth($pesel, DateTime::createFromFormat('Y-m-d', $birthDate));

        $this->assertEquals(
            $isCorrect,
            $actual,
            "Invalid gender. Got $actual, expected $isCorrect for number $pesel and birth date $birthDate"
        );
    }

    /**
     * @dataProvider genderDataProvider
     * @param Pesel $pesel
     * @param int $gender
     * @param bool $isCorrect
     */
    public function testHasGenderReturnsCorrectValue(Pesel $pesel, $gender, $isCorrect)
    {
        $actual = PeselValidator::hasGender($pesel, $gender);

        $a = $actual ? 'true' : 'false';
        $b = $isCorrect ? 'true' : 'false';

        $this->assertEquals(
            $isCorrect,
            $actual,
            "Invalid gender. Got {$a}, expected {$b} for number $pesel and gender $gender"
        );
    }

    /**
     * @dataProvider genderInputsDataProvider
     * @param mixed $gender
     * @param bool $isValid
     */
    public function testHasGenderThrowsExceptionWhenGenderInputIsInvalid($gender, $isValid)
    {
        $pesel = new Pesel("00010100008");

        if ($isValid == false) {
            $this->setExpectedException('InvalidArgumentException', 'Podano płeć w niepoprawnym formacie');
        }

        PeselValidator::hasGender($pesel, $gender);
    }

    public function birthDateDataProvider()
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

    public function genderDataProvider()
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

    public function genderInputsDataProvider()
    {
        return [
            ['M', false],
            ['m', false],
            ['F', false],
            ['f', false],
            ['K', false],
            ['k', false],
            ['W', false],
            ['w', false],
            [Pesel::GENDER_MALE, true],
            [Pesel::GENDER_FEMALE, true],
            [0, true],
            [1, true],
            ['0', true],
            ['1', true],
        ];
    }

}