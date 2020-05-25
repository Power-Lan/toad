<?php

namespace Toad\TestExecutor;
use Toad\Exception;

trait Gpio
{
    private $gpioFd = array();

    function initGpios()
    {
        foreach ($this->config['gpio'] as $key => $value) {
            $this->info('gpio: init ' . $value . ' (' . $key . ')');
            $path = '/sys/class/gpio/gpio' . $value . '/value';
            $fd = fopen($path, 'w+');
            if ($fd === false) {
                throw new Exception('Can NOT open ' . $path);
            }
            $this->gpioFd[$key] = $fd;
        }
    }

    function configureGpios(array $config)
    {
        foreach ($config as $key => $value) {
            $this->configureGpio($key, $value);
        }
    }

    function configureGpio(string $name, $index)
    {
        if (array_key_exists('gpio', $this->config) === false) {
          $this->config['gpio'] = array();
        }

        if (isset($this->config['gpio'][$name])) {
            if ($this->config['gpio'][$name] !== (string) $index) {
                $msg = sprintf('Already declared GPIO %1 with value %2', $name, $this->config['gpio'][$name]);
                throw new Exception($msg);
            }
        }

        $this->config['gpio'][$name] = (string) $index;
    }

    function getGpioIndex(string $name)
    {
        if (isset($this->config['gpio'][$name])) {
            return $this->config['gpio'][$name];
        }

        throw new Exception('gpio: ' . $name . ' is unknown');
    }

    function writeGpio(string $name, int $value)
    {
        $this->verbose('gpio: writing ' . $value . ' on ' . $name);

      // Ensure GPIO is declared
        $index = $this->getGpioIndex($name);

      // Ensure GPIO is open
        if (isset($this->gpioFd[$name]) === false) {
            throw new Exception('gpio: ' . $name . ' is declared but not init');
        }

        $rc = fwrite($this->gpioFd[$name], "$value\n");
        if ($rc === false) {
            throw new Exception('gpio: write on ' . $name . ' have failed');
        }
    }

    function readGpio(string $name)
    {
      $this->verbose('gpio: reading ' . $name);

    // Ensure GPIO is declared
      $index = $this->getGpioIndex($name);

    // Ensure GPIO is open
      if (isset($this->gpioFd[$name]) === false) {
          throw new Exception('gpio: ' . $name . ' is declared but not init');
      }

      fseek($this->gpioFd[$name], 0);
      $str = fread($this->gpioFd[$name], 32);
      if ($str === false) {
          throw new Exception('gpio: read on ' . $name . ' have failed');
      }

      $value = (int) $str;
      $this->verbose('gpio: readed ' . $value . ' on ' . $name);
      return $value;
    }
}
