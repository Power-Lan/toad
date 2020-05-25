<?php

namespace Toad\Service\TTN;

trait Applications
{
  public function deleteApplication(string $app)
  {
    return $this->delete($this->getHost() . "/applications/$app");
  }

  public function getApplication(string $app)
  {
    return $this->getJson($this->getHost() . "/applications/$app");
  }

  public function getApplicationDevices(string $app)
  {
    return $this->getJson($this->getHost() . "/applications/$app/devices");
  }
}
