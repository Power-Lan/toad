<?php

namespace Toad\Device;

class TMC
{
  private $fd = null;

  function __construct($device)
  {
    $this->fd = fopen($device, 'w+');
    if ($this->fd === false) {
      throw new Exception("Can not open $device");
    }
  }

  public function ask($cmd)
  {
    fwrite($this->fd, $cmd);
    return trim(fgets($this->fd, 512));
  }

  public function readIdentification()
  {
    return $this->ask('*IDN?');
  }

  static public function scan()
  {
    $out = array();

    $devices = glob('/dev/usbtmc*');
    foreach ($devices as $device) {

      $dev = new self($device);
      $name = $dev->readIdentification();

      $out[] = array(
        'device' => $device,
        'name' => $name
      );
    }

    return $out;
  }
}
