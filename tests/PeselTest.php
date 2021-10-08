<?php

namespace Pesel\Tests;

use InvalidArgumentException;
use Pesel\Pesel;
use PHPUnit\Framework\TestCase;

class PeselTest extends TestCase
{
    /**
     * @dataProvider invalidNumberDataProvider
     *
     * @param mixed $number
     */
    public function testExceptionIsThrownWhenNumberIsInvalid($number)
    {
        $this->expectException(InvalidArgumentException::class);

        Pesel::create($number);
    }

    /**
     * @dataProvider invalidNumberDataProvider
     *
     * @param mixed $number
     */
    public function testIsValidReturnsFalseWhenNumberIsInvalid($number)
    {
        $actual = Pesel::isValid($number);

        $this->assertFalse($actual);
    }

    /**
     * @dataProvider validNumberDataProvider
     * @doesNotPerformAssertions
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testNoExceptionIsThrownWhenNumberIsValid(string $number, string $birthDate, int $gender)
    {
        try {
            Pesel::create($number);
        } catch (InvalidArgumentException $e) {
            $this->fail("Unexpected InvalidArgumentException: {$e->getMessage()}");
        }
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testIsValidReturnsTrueWhenNumberIsValid(string $number, string $birthDate, int $gender)
    {
        $actual = Pesel::isValid($number);

        $this->assertTrue($actual);
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testGetBirthDateReturnsCorrectDate(string $number, string $birthDate, int $gender)
    {
        $pesel = Pesel::create($number);

        $actual = $pesel->getBirthDate()->format('Y-m-d');

        $this->assertEquals(
            $birthDate,
            $actual,
            "Invalid birth date. Got $actual, expected $birthDate for number $number"
        );
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testGetGenderReturnsCorrectGender(string $number, string $birthDate, int $gender)
    {
        $pesel = Pesel::create($number);

        $actual = $pesel->getGender();

        $this->assertEquals(
            $gender,
            $actual,
            "Invalid gender. Got $actual, expected $gender for number $number"
        );
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testGetNumberReturnsCorrectNumber(string $number, string $birthDate, int $gender)
    {
        $pesel = Pesel::create($number);

        $actual = $pesel->getNumber();

        $this->assertEquals(
            $number,
            $actual,
            "Invalid gender. Got $actual, expected $number for number $number"
        );
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testToStringReturnsCorrectNumber(string $number, string $birthDate, int $gender)
    {
        $pesel = Pesel::create($number);

        $actual = (string) $pesel;

        $this->assertEquals(
            $number,
            $actual,
            "Invalid gender. Got $actual, expected $number for number $number"
        );
    }

    public function testCustomInvalidLengthMessage()
    {
        $errorMessages = [
            'invalidLength' => 'invalidLength',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidLength']);

        new Pesel('1234', $errorMessages);
    }

    public function testCustomInvalidCharactersMessage()
    {
        $errorMessages = [
            'invalidCharacters' => 'invalidCharacters',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidCharacters']);

        new Pesel('aaaabbbbccc', $errorMessages);
    }

    public function testCustomInvalidChecksumMessage()
    {
        $errorMessages = [
            'invalidChecksum' => 'invalidChecksum',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidChecksum']);

        new Pesel('11111111111', $errorMessages);
    }

    public function testCustomInvalidBirthDateMessage()
    {
        $errorMessages = [
            'invalidBirthDate' => 'invalidBirthDate',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidBirthDate']);

        new Pesel('44444444444', $errorMessages);
    }

    public function invalidNumberDataProvider()
    {
        return [
            [1234],
            ['1234'],
            ['aaaa'],
            ['11111111111'],
            ['aaaaaaaaaaa'],
            ['96100612532'],
            ['61122500187'],
            ['78091501150'],
            ['00000000000'],
            ['44444444444'],
        ];
    }

    public function validNumberDataProvider()
    {
        return [
            ['83082317338', '1983-08-23', Pesel::GENDER_MALE],
            ['83082317338', '1983-08-23', Pesel::GENDER_MALE],
            ['62022216858', '1962-02-22', Pesel::GENDER_MALE],
            ['80052117613', '1980-05-21', Pesel::GENDER_MALE],
            ['40041212350', '1940-04-12', Pesel::GENDER_MALE],
            ['93080611761', '1993-08-06', Pesel::GENDER_FEMALE],
            ['08232314148', '2008-03-23', Pesel::GENDER_FEMALE],
            ['97100911864', '1997-10-09', Pesel::GENDER_FEMALE],
            ['45021009506', '1945-02-10', Pesel::GENDER_FEMALE],
            ['50011703922', '1950-01-17', Pesel::GENDER_FEMALE],
            ['00810100002', '1800-01-01', Pesel::GENDER_FEMALE],
            ['50910100000', '1850-11-01', Pesel::GENDER_FEMALE],
            ['00010100008', '1900-01-01', Pesel::GENDER_FEMALE],
            ['50110100006', '1950-11-01', Pesel::GENDER_FEMALE],
            ['00210100004', '2000-01-01', Pesel::GENDER_FEMALE],
            ['50310100002', '2050-11-01', Pesel::GENDER_FEMALE],
            ['00410100000', '2100-01-01', Pesel::GENDER_FEMALE],
            ['50510100008', '2150-11-01', Pesel::GENDER_FEMALE],
            ['00610100006', '2200-01-01', Pesel::GENDER_FEMALE],
            ['50710100004', '2250-11-01', Pesel::GENDER_FEMALE],
        ];
    }
}
