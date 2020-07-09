<?php

namespace Toad;

use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request;
use RuntimeException;
use Toad;

class Network
{
    private $context = null;
    private $retry = 3;
    private $timeout = 10;
    private $headers = array();

    public function __construct(Toad\TestExecutor $context)
    {
        $this->context = $context;
    }

    public function setTimeout(int $sec = 10)
    {
        $this->timeout = $sec;
    }

    public function setRetryCount(int $count = 3)
    {
        $this->retry = $count;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    private function __exec(Request $request, int $timeout)
    {
        $adapter = GuzzleAdapter::createWithConfig([
            'timeout' => $timeout
        ]);

        // Retry : http://docs.php-http.org/en/latest/plugins/introduction.html

        $retry = $this->retry;
        while ($retry--) {
          try {
              return $adapter->sendRequest($request);
          } catch (RuntimeException $e) {
              $this->context->error("http: " . $e->getMessage());
              throw $e;
          }

          sleep(1);
        }
    }

    function get(string $url, int $timeout = 0)
    {
        $timeout = $timeout ?? $this->timeout;

        try {
            $this->context->info("get: $url");
            $request = new Request('GET', $url, $this->headers);
            $response = $this->__exec($request, $timeout);

            $status = $response->getStatusCode();
            $this->context->info("get: http code $status");
            if ($status !== 200) {
                return false;
            }

            $this->context->info("get: success");
            return $response->getBody();
        } catch (RuntimeException $e) {
            $this->context->error("get: " . $e->getMessage());
        }

        return false;
    }

    function delete(string $url, int $timeout = 0)
    {
        $timeout = $timeout ?? $this->timeout;

        try {
            $this->context->info("delete: $url");
            $request = new Request('DELETE', $url, $this->headers);
            $response = $this->__exec($request, $timeout);
            $rc = $response->getStatusCode();
            if ($rc >= 200 && $rc < 300) {
                return false;
            }

            $this->context->info("delete: success");
            return $response->getBody();
        } catch (RuntimeException $e) {
            $this->context->error("delete: " . $e->getMessage());
        }

        return false;
    }

    function getJson(string $url, int $timeout = 30)
    {
        $body = $this->get($url, $timeout);
        if ($body === false){
            return false;
        }

        $json = json_decode($body);
        if ($json === false) {
            $this->context->error("json: failed to decode");
            return false;
        }

        return $json;
    }

    function postJson(string $url, $payload, int $timeout = 30)
    {
      $timeout = $timeout ?? $this->timeout;

      try {
          $this->context->info("post: $url");

          $headers = array_merge($this->headers, [
              'Content-Type' => 'application/json',
          ]);
          $request = new Request('POST', $url, $headers, json_encode($payload));
          $response = $this->__exec($request, $timeout);

          $status = $response->getStatusCode();
          $this->context->info("post: http code $status");

          return $response->getBody();
      } catch (RuntimeException $e) {
          $this->context->error("post: " . $e->getMessage());
      }

      return false;
    }

}
