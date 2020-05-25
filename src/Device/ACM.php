<?php

namespace Toad\Device;

class ACM
{
  protected $fd = null;

  function __construct($device)
  {
    $this->fd = fopen($device, 'w+');
    if ($this->fd === false) {
      throw new Exception("Can not open $device");
    }
  }

  static public function scan()
  {
    $out = array();

    $devices = glob('/dev/ttyACM*');
    foreach ($devices as $device) {

      $out[] = array(
        'device' => $device,
      );
    }

    return $out;
  }
}
