<?php

namespace Toad\Service\TTN;

/*
 * We need an Access Keys for the account to use Applications API
 */
trait Application
{
  private string $applicationId = '';
  private string $applicationKey = '';

  public function selectApplicationContext(string $applicationId, string $applicationKey = '')
  {
    $this->applicationId = $applicationId;
    $this->applicationKey = $applicationKey;
  }

  public function getDevices()
  {
    $url = sprintf('https://%s/api/v3/applications/%s/devices', $this->getHost(), $this->applicationId);
    $response = $this->getJson($url);

    return $response->end_devices;
  }

  public function getDevice($devId)
  {
    $url = sprintf('https://%s/api/v3/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
    return $this->getJson($url);
  }

  public function getNsDevice($devId)
  {
    $url = sprintf('https://%s/api/v3/applications/%s/devices/%s?field_mask=session.dev_addr', $this->getHost(), $this->applicationId, $devId);
    return $this->getJson($url);
  }
  
  public function deleteDevice($devId)
  {
    $url = sprintf('https://%s/api/v3/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
    return $this->delete($url);
  }

  public function createDevice($devId, $devEUI, $appEUI)
  {
    if (ctype_xdigit($devEUI) === false || strlen($devEUI) != 16) {
      throw new Exception('Device EUI must be 8 bytes encoded in an hexadecimal string');
    }

    $applicationId = $this->applicationId;
    $applicationKey = $this->applicationKey;

    // https://github.com/TheThingsNetwork/lorawan-stack/releases
    $bin = $this->context->getBin('ttn-lw-cli');
    $opts = "end-devices create $applicationId $devId --dev-eui $devEUI --join-eui $appEUI --frequency-plan-id EU_863_870_TTN --root-keys.app-key.key $applicationKey --lorawan-version 1.0.1 --lorawan-phy-version 1.0.1";
    $rc = $this->context->execute("$bin $opts");
    if ($rc !== 0) {
        $this->context->info('ttn-lw-cli: command failed');
        return false;
    }

    return true;
  }

  /*

  NE MARCHE PAS

  public function createDeviceUsingAPI($devId, $devEUI, $appEUI)
  {
    if (ctype_xdigit($devEUI) === false || strlen($devEUI) != 16) {
      throw new Exception('Device EUI must be 8 bytes encoded in an hexadecimal string');
    }
  
    if (true) {
      // Create device
      $url = sprintf('https://%s/api/v3/applications/%s/devices', $this->getHost(), $this->applicationId);
      $payload = [
        'end_device' => array(
          'application_server_address' => $this->getHost(),
          'ids' => array(
            'dev_eui' => $devEUI,
            'device_id' => $devId,
            'join_eui' => $appEUI,
          ),
          'join_server_address' => $this->getHost(),
          'network_server_address' => $this->getHost(),
        ),
      ];
      var_dump(json_encode($payload));
      $rc = $this->postJson($url, json_encode($payload));
      var_dump($rc);
    }

    if (true) {
      // Register name server
      $url = sprintf('https://%s/api/v3/ns/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
      $payload = [
        'end_device' => array(
          'frequency_plan_id' => "EU_863_870_TTN",
          'lorawan_phy_version' => 'PHY_V1_0_1',
          'multicast' => false,
          'supports_join' => true,
          'lorawan_version' => 'MAC_V1_0_1',
          'ids' => array(
            'dev_eui' => $devEUI,
            'join_eui' => $appEUI,
            'device_id' => $devId,
          ),
          'supports_class_c' => false,
          'supports_class_b' => false,
          'mac_settings' => array(
            "rx2_data_rate_index" => 0,
            "rx2_frequency" => 869525000
          ),
        ),
      ];
      var_dump(json_encode($payload));
      $rc = $this->putJson($url, json_encode($payload));
      var_dump($rc);
    }

    // Register app server
    if (false) {
      $url = sprintf('https://%s/api/v3/as/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
      $payload = [
        'end_device' => array(
          'ids' => array(
            'dev_eui' => $devEUI,
            'device_id' => $devId,
            'join_eui' => $appEUI,
          ),
        ),
      ];
      var_dump(json_encode($payload));
      $rc = $this->putJson($url, json_encode($payload));
      var_dump($rc);
    }

    // Register join server
    if (false) {
      $url = sprintf('https://%s/api/v3/js/applications/%s/devices/%s', $this->getHost(), $this->applicationId, $devId);
      $payload = [
        'end_device' => array(
          'application_server_address' => $this->getHost(),
          'ids' => array(
            'dev_eui' => $devEUI,
            'device_id' => $devId,
            'join_eui' => $appEUI,
          ),
          'network_server_address' => $this->getHost(),
          'root_keys' => array(
            'app_key' => array(
              'key' => $this->applicationKey,
            )
          )
        ),
      ];
      var_dump(json_encode($payload));
      $rc = $this->putJson($url, json_encode($payload));
      var_dump($rc);
    }

    return true;
  }
  */
}
