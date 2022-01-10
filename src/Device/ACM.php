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

  function __destruct()
  {
    fclose($this->fd);
  }

  function readAll() : string
  {
    $out = '';

    stream_set_blocking($this->fd, 0);

    while (true) {
      if (feof($this->fd)) {
        break;
      }

      $chunk = fread($this->fd, 8192);
      if ($chunk === '') {
        break;
      }

      $out .= $chunk;
    }

    stream_set_blocking($this->fd, 1);

    return $out;
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

