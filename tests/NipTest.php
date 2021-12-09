<?php

namespace Pesel\Tests;

use InvalidArgumentException;
use Pesel\Nip;
use Pesel\Pesel;
use PHPUnit\Framework\TestCase;

class NipTest extends TestCase
{
    /**
     * @dataProvider invalidNumberDataProvider
     *
     * @param mixed $number
     */
    public function testExceptionIsThrownWhenNumberIsInvalid($number)
    {
        $this->expectException(InvalidArgumentException::class);

        Nip::create($number);
    }

    /**
     * @dataProvider invalidNumberDataProvider
     *
     * @param mixed $number
     */
    public function testIsValidReturnsFalseWhenNumberIsInvalid($number)
    {
        $actual = Nip::isValid($number);

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
    public function testNoExceptionIsThrownWhenNumberIsValid($number)
    {
        try {
            Nip::create($number);
        } catch (InvalidArgumentException $e) {
            var_dump($number);
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
    public function testIsValidReturnsTrueWhenNumberIsValid($number)
    {
        $actual = Nip::isValid($number);

        $this->assertTrue($actual);
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testGetNumberReturnsCorrectNumber($number)
    {
        $nip = Nip::create($number);

        $actual = $nip->getNumber();

        $this->assertEquals(
            $number,
            $actual,
            "Invalid number from getNumber(). Got $actual, expected $number for number $number"
        );
    }

    /**
     * @dataProvider validNumberDataProvider
     *
     * @param string $number
     * @param string $birthDate
     * @param int    $gender
     */
    public function testToStringReturnsCorrectNumber($number)
    {
        $nip = Nip::create($number);

        $actual = (string) $nip;

        $this->assertEquals(
            $number,
            $actual,
            "Invalid number from __toString(). Got $actual, expected $number for number $number"
        );
    }

    public function testCustomInvalidLengthMessage()
    {
        $errorMessages = [
            'invalidLength' => 'invalidLength',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidLength']);

        new Nip('1234', $errorMessages);
    }

    public function testCustomInvalidCharactersMessage()
    {
        $errorMessages = [
            'invalidCharacters' => 'invalidCharacters',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidCharacters']);

        new Nip('aaaabbbbcc', $errorMessages);
    }

    public function testCustomInvalidChecksumMessage()
    {
        $errorMessages = [
            'invalidChecksum' => 'invalidChecksum',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errorMessages['invalidChecksum']);

        new Nip('1234567890', $errorMessages);
    }

    public function invalidNumberDataProvider()
    {
        return [
            [1234],
            ['1234'],
            ['aaaa'],
            ['aaaaaaaaaa'],
            ['1234567890'],
            ['1234563219'],
            [1234567890],
            [1234563219],
        ];
    }

    public function validNumberDataProvider()
    {
        return [
            ['0000000000'],
            ['5272944982'],
            [5272944982],
        ];
    }
}
