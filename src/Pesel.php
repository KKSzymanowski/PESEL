<?php

namespace Pesel;

use Pesel\Traits\BasicValidation;
use Pesel\Traits\BirthDateValidation;
use Pesel\Traits\GenderValidation;

class Pesel implements PeselInterface
{

    use BasicValidation;
    use GenderValidation;
    use BirthDateValidation;
    
    protected $number;

    /**
     * Pesel constructor.
     * @param string $number
     */
    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * A glorified constructor.
     *
     * @param string $number
     * @return static
     */
    public static function create($number)
    {
        return new static($number);
    }

}
