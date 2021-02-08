<?php

namespace Sioweb\AcSignature\Classes;

class MissingParameterException extends \Exception
{
    protected $missingParameter;

    public function __construct($missingParameter) {
        $this->missingParameter = $missingParameter;
        parent::__construct();
    }
  
    public function __toString() {
      return 'Parameter ' . $this->missingParameter . ' is missing!';
    }  
}