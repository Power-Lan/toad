<?php

namespace Toad\Device\TTI;
use Toad\Device\ACM;

class EL302P extends ACM
{
  public $usbVendor = 0x103e;
  public $usbProduct = 0x049c;

  public function setVoltage(float $value)
  {
    fwrite($this->fd, "V $value\n");
  }

  public function setCurrentLimit(float $value)
  {
    fwrite($this->fd, "I $value\n");
  }

  public function on()
  {
    fwrite($this->fd, "ON\n");
  }

  public function off()
  {
    fwrite($this->fd, "OFF\n");
  }

  public function readIdentification()
  {
    fwrite($this->fd, "*IDN?\n");
    return trim(fgets($this->fd, 512));
  }
}
