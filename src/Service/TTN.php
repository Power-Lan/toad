<?php

namespace Toad\Service;
use Toad;

class TTN extends Toad\Network
{
  use TTN\Application;

  private $region = '';

  public function __construct(Toad\TestExecutor $context, string $region = 'eu1')
  {
    parent::__construct($context);
    $this->setRegion($region);
  }

  public function setRegion(string $region)
  {
    $this->region = $region;
  }

  protected function getHost()
  {
    return sprintf("%s.cloud.thethings.network", $this->region);
  }

  public function setAccessKey(string $key)
  {
    $this->setHeaders([
      'Authorization' => 'Bearer ' . $key
    ]);
  }
}
