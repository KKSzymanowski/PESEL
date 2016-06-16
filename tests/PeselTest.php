<?php

use Carbon\Carbon;
use Pesel\Pesel;

class PeselTest extends PHPUnit_Framework_TestCase
{

    public function testLengthValidation()
    {
        $this->assertFalse(Pesel::create("1234")->hasValidLength());

        $this->assertTrue(Pesel::create("12341234123")->hasValidLength());
    }

    public function testOnlyDigitValidation()
    {
        $this->assertFalse(Pesel::create("1234a1234")->containsOnlyDigits());

        $this->assertTrue(Pesel::create("12341234")->containsOnlyDigits());
    }

    public function testChecksumValidation()
    {
        $pesels = [
            '95100612532' => true,
            '96100612532' => false,
            '60122500187' => true,
            '61122500187' => false,
            '77091501150' => true,
            '78091501150' => false,
        ];

        foreach ($pesels as $pesel => $valid) {
            $pesel = Pesel::create($pesel);
            $this->assertTrue($pesel->hasValidLength());

            $this->assertTrue($pesel->containsOnlyDigits());

            $this->assertEquals($valid, $pesel->hasValidChecksum());

            $this->assertEquals($valid, $pesel->isValid());
        }
    }

    public function testExceptionIsThrownWhenInvalidGenderIsProvided()
    {
        $this->setExpectedException('InvalidArgumentException');

        foreach (['0', '1', 0, 1, 'A', 'b', 'c', 'd'] as $gender) {
            Pesel::create("00000000000")->hasGender($gender);
        }
    }

    public function testExceptionIsNotThrownWhenValidGenderIsProvided()
    {
        try {
            foreach (['M', 'm', 'K', 'k', 'W', 'w', 'F', 'f', 0, 1] as $gender) {
                Pesel::create("00000000000")->hasGender($gender);
            }
        } catch (InvalidArgumentException $e) {
            $this->fail("Unexpected exception " . $e->getMessage() . " was thrown");
        }
    }

    public function testGenderValidation()
    {
        for ($i = 0; $i < 10; ++$i) {
            foreach (['m', 'M', 1, Pesel::GENDER_MALE] as $gender) {
                $this->assertEquals($i % 2 == 1, Pesel::create("000000000${i}0")->hasGender($gender));
            }
            foreach (['K', 'k', 'W', 'w', 'F', 'f', 0, Pesel::GENDER_FEMALE] as $gender) {
                $this->assertEquals($i % 2 == 0, Pesel::create("000000000${i}0")->hasGender($gender));
            }
        }
    }

    public function testBirthDateValidation()
    {
        $dates = [
            '008101' => Carbon::createFromFormat('Y-m-d', '1800-01-01'),
            '509101' => Carbon::createFromFormat('Y-m-d', '1850-11-01'),
            '000101' => Carbon::createFromFormat('Y-m-d', '1900-01-01'),
            '501101' => Carbon::createFromFormat('Y-m-d', '1950-11-01'),
            '002101' => Carbon::createFromFormat('Y-m-d', '2000-01-01'),
            '503101' => Carbon::createFromFormat('Y-m-d', '2050-11-01'),
            '004101' => Carbon::createFromFormat('Y-m-d', '2100-01-01'),
            '505101' => Carbon::createFromFormat('Y-m-d', '2150-11-01'),
            '006101' => Carbon::createFromFormat('Y-m-d', '2200-01-01'),
            '507101' => Carbon::createFromFormat('Y-m-d', '2250-11-01'),
        ];

        foreach (array_keys($dates) as $actual) {
            foreach ($dates as $different => $date) {
                $this->assertEquals(
                    $actual == $different,
                    Pesel::create($actual . '00000')->hasBirthDate($date)
                );

                $this->assertEquals(
                    $actual == $different,
                    Pesel::create($actual . '00000')->hasDateOfBirth($date)
                );
            }

        }

    }

    public function testBasicValidation()
    {

    }
}