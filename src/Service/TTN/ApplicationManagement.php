<?php

namespace Toad\Service\TTN;

/*
 * We need an Access Keys for the account to use Applications API
 */

trait ApplicationManagement
{
/*
  public function registerApplication(string $userId, string $applicationId)
  {
    $payload = [
      'application' => array(
        'ids' => $applicationId,
      )
    ];
    return $this->postJson($this->getHost() . "/api/v3/users/$userId/applications", $payload);
  }

  public function deleteApplication(string $applicationId)
  {
    $payload = [
      'application_id' => $applicationId
    ];
    return $this->delete($this->getHost() . "/api/v3/applications/$applicationId", $payload);
  }
*/

  public function getApplication(string $applicationId)
  {
    return $this->getJson('https://' . $this->getHost() . "/api/v3/applications/$applicationId");
  }
}
