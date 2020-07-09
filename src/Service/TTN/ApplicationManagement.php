<?php

namespace Toad\Service\TTN;

/*
 * We need an Access Keys for the account to use Applications API
 */

trait ApplicationManagement
{
  public function registerApplication(string $applicationId)
  {
    $payload = [
      'app_id' => $applicationId
    ];
    return $this->postJson($this->getHost() . "/applications", $payload);
  }

  public function deleteApplication(string $applicationId)
  {
    return $this->delete($this->getHost() . "/applications/$applicationId");
  }

  public function getApplication(string $applicationId)
  {
    return $this->getJson($this->getHost() . "/applications/$applicationId");
  }
}
