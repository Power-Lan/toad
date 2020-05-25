<?php

namespace Toad\Device\Keithley;
use Toad\Device\TMC;

class Model2110 extends TMC
{
  public $usbVendor = 0x05e6;
  public $usbProduct = 0x2110;

  public function readVoltageDC()
  {
    return (float) $this->ask(':MEASure:VOLTage?');
  }

  public function readVoltageAC()
  {
    return (float) $this->ask(':MEASure:VOLTage:AC?');
  }

  public function readCurrentDC()
  {
    return (float) $this->ask(':MEASure:CURRent?');
  }

  public function readCurrentAC()
  {
    return (float) $this->ask(':MEASure:CURRent:AC?');
  }
}
