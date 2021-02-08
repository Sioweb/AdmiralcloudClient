<?php

namespace Sioweb\AdmiralcloudClient\Classes;

class DatatypeException extends \Exception
{
    protected $parameter;
    protected $expectedType;
    protected $detectedType;

    public function __construct($parameter, $expectedType, $detectedType) {
        $this->parameter = $parameter;
        $this->expectedType = $expectedType;
        $this->detectedType = $detectedType;
        parent::__construct();
    }
  
    public function __toString() {
      return 'Parameter ' . $this->parameter . ' must be typeof ' . $this->expectedType . ', ' . $this->detectedType . ' is given!';
    }  
}