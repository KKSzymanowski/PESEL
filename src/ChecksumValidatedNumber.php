<?php

namespace Pesel;

use InvalidArgumentException;
use Pesel\Exceptions\InvalidCharactersException;
use Pesel\Exceptions\InvalidChecksumException;
use Pesel\Exceptions\InvalidLengthException;

abstract class ChecksumValidatedNumber
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @return string[]
     */
    protected abstract function getDefaultErrorMessages();

    /**
     * @return int[]
     */
    protected abstract function getWeights();

    /**
     * @return int
     */
    protected abstract function getModulus();

    /**
     * @param string $number
     * @param array  $errorMessages Deprecated, please catch a specific exception extending PeselValidationException
     *
     * @throws InvalidArgumentException when the number is invalid.
     */
    public function __construct($number, $errorMessages = [])
    {
        $this->errorMessages = array_merge($this->getDefaultErrorMessages(), $errorMessages);

        $this->number = (string) $number;

        $this->validateLength();

        $this->validateDigitsOnly();

        $this->validateChecksum();
    }

    /**
     * A glorified constructor.
     *
     * @param string $number
     *
     * @throws InvalidArgumentException when number is invalid.
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
        if (strlen($this->number) !== $this->expectedLength()) {
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
        $weights = $this->getWeights();

        $checksum = 0;

        for ($i = 0; $i < count($weights); ++$i) {
            $checksum += $weights[$i] * $digitArray[$i];
        }

        $checksum = $checksum % $this->getModulus();

        if ($this->reverseChecksum()) {
            $checksum = ($checksum == 0) ? 0 : $this->getModulus() - $checksum;
        }

        if ($checksum !== (int) $digitArray[count($digitArray) - 1]) {
            throw new InvalidChecksumException($this->errorMessages['invalidChecksum']);
        }
    }

    /**
     * Calculate the correct number length based on the weights.
     *
     * @return int
     */
    protected function expectedLength()
    {
        return count($this->getWeights()) + 1;
    }

    /**
     * Should the checksum be subtracted from the modulus like in the case of the PESEL number?
     *
     * @return boolean
     */
    protected function reverseChecksum()
    {
        return false;
    }
}
