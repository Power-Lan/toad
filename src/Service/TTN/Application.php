<?php

namespace Toad\Service\TTN;

/*
 * We need an Access Keys for the account to use Applications API
 */

trait Application
{
  private $applicationId = '';
  private $applicationEUI = '';

  public function setApplicationContext(string $applicationId, string $applicationEUI, string $accessKey)
  {
    if (ctype_xdigit($applicationEUI) === false || strlen($applicationEUI) != 16) {
      throw new Exception('Application EUI must be 8 bytes encoded in an hexadecimal string');
    }

    $this->applicationId = $applicationId;
    $this->applicationEUI = $applicationEUI;
    $this->setAccessKey($accessKey);
  }

  public function getDevices()
  {
    $url = sprintf('%s/applications/%s/devices', $this->getHost(), $this->applicationId);
    $response = $this->getJson($url);

    return $response->devices;
  }

  public function getDevice($devId)
  {
    $url = sprintf('%s/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
    return $this->getJson($url);
  }

  public function deleteDevice($devId)
  {
    $url = sprintf('%s/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
    return $this->delete($url);
  }

  public function createDevice($devId, $devEUI)
  {
    if (ctype_xdigit($devEUI) === false || strlen($devEUI) != 16) {
      throw new Exception('Device EUI must be 8 bytes encoded in an hexadecimal string');
    }

    $url = sprintf('%s/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
    $payload = [
      "app_id" => $this->applicationId,
      "dev_id" => $devId,
      "lorawan_device" => [
        "activation_constraints" => "local",
        "app_eui" => $this->applicationEUI,
        "app_id" => $this->applicationId,
        "app_key" => strtoupper(bin2hex(random_bytes(16))),
        "dev_eui" => $devEUI,
        "dev_id" => $devId,
        "disable_f_cnt_check" => false,
        "uses32_bit_f_cnt" => true
      ]
    ];

    var_dump($payload);

    return $this->postJson($url, $payload);
  }
}
