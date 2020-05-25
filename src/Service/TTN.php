<?php

namespace Toad\Service;
use Toad;

class TTN extends Toad\Network
{
  use TTN\Applications;

  private $region = 'eu';

  public function __construct(Toad\TestExecutor $context)
  {
    parent::__construct($context);
  }

  public function setRegion(string $region)
  {
    $this->region = $region;
  }

  protected function getHost()
  {
    return sprintf("http://%s.thethings.network:8084", $this->region);
  }

  public function setAccessKey(string $key)
  {
    $this->setHeaders([
      'Authorization' => 'Key ' . $key
    ]);
  }
}
