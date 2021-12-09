<?php

namespace Pesel;

class Nip extends ChecksumValidatedNumber
{
    /**
     * @inheritdoc
     */
    public function __construct($number, $errorMessages = [])
    {
        parent::__construct($number, $errorMessages);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultErrorMessages()
    {
        return [
            'invalidLength'     => 'Nieprawidłowa długość NIP.',
            'invalidCharacters' => 'NIP może zawierać tylko cyfry.',
            'invalidChecksum'   => 'NIP posiada niepoprawną sumę kontrolną.',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getWeights()
    {
        return [6, 5, 7, 2, 3, 4, 5, 6, 7];
    }

    /**
     * @inheritdoc
     */
    protected function getModulus()
    {
        return 11;
    }
}
