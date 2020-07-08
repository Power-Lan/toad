<?php

namespace Toad;

class TestExecutor
{
    use TestExecutor\Env;
    use TestExecutor\Execute;
    use TestExecutor\Log;
    use TestExecutor\Gpio;
    use TestExecutor\Asserts;
    use TestExecutor\Registry;

    private $config = array();
    private $deviceStatus = array();
    private $step = 0;
    private $description = null;
    private $pendding = array();
    private $tstart = 0;
    private $rc = -1;

    function __construct(array $conf)
    {
        $this->setConfig($conf);
        $this->initLog();
    }

    function start(string $description): void
    {
        $this->tstart = microtime(true);
        $this->step++;
        $this->description = $description;
        $this->header(sprintf("[START] Step %d: %s", $this->step, $this->description));
    }

    function finish(bool $success): void
    {
        $duration = microtime(true) - $this->tstart;
        if ($success === true) {
            $this->headerSuccess(sprintf("[OK   ] Step %d: %s (%0.3f sec)", $this->step, $this->description, $duration));
        } else {
            $this->headerError(sprintf("[ERROR] Step %d: %s (%0.3f sec)", $this->step, $this->description, $duration));
        }

        $this->description = null;
    }

    function showFinalStatus(): void
    {
      // Show a final status
      if ($this->getReturnCode() == 0) {
        $this->compose(array(
          array(array("bg_light_green", "black"), '[FINAL STATUS]'),
          array(array("bg_light_green", "black", "blink"), ' ----->>> '),
          array(array("bg_light_green", "black"), 'Device Ok'),
          array(array("bg_light_green", "black", "blink"), ' <<<----- '),
          array(array("bg_light_green", "black"), "\x1B[K"),
        ));
      } else {
        $this->compose(array(
          array(array("bg_light_red", "black"), '[FINAL STATUS]'),
          array(array("bg_light_red", "black", "blink"), ' ----->>> '),
          array(array("bg_light_red", "black"), 'Device Error'),
          array(array("bg_light_red", "black", "blink"), ' <<<----- '),
          array(array("bg_light_red", "black"), "\x1B[K"),
        ));
      }
    }

    function setDeviceStatus(string $key, $value)
    {
        $this->deviceStatus[$key] = $value;
    }

    function getDeviceStatus(string $key)
    {
        return $this->deviceStatus[$key] ?? null;
    }

    function setConfig(array $conf): void
    {
        $this->config = $conf;

      // Ensure the binary section exists
        if (isset($this->config['bin']) === false || is_array($this->config['bin']) === false) {
            $this->config['bin'] = array();
        }
    }

    function getConfig(): array
    {
        return $this->config;
    }

    function setConf(string $key, $value)
    {
        $this->config[$key] = $value;
    }

    function getConf(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    function getBin(string $name)
    {
        return $this->config['bin'][$name] ?? '/bin/false';
    }

    function appendTests(array $tests)
    {
        foreach ($tests as $test) {
            if (is_string($test)) {
                $this->pendding[] = array(
                  'class' => $test,
                  'options' => array()
                );
            } elseif (is_array($test)) {
                $this->pendding[] = $test;
            } else {
                throw new Exception('Format invalid pour ajouter des tests');
            }
        }
    }

    function ensureBinaryExists()
    {
        $bins = $this->getConf('bin', array());
        foreach ($bins as $name => &$path) {
            if ($path === null) {
                $this->info('bin: auto-detecting ' . $name);

                $output = array();
                $rc = 0;
                $content = exec('which ' . $name, $output, $rc)   ;
                if ($rc === 0) {
                    $path = $content;
                }
            }

            if ($path !== null) {
                $this->_ensureBinaryExists($path);
            } else {
                $this->error('bin: ' . $name . ', not found');
                throw new Exception('bin: auto-detecting failed');
            }
        }

        $this->setConf('bin', $bins);
    }

    function _ensureBinaryExists(string $path)
    {
        $this->info('bin: ' . $path);

        if (file_exists($path) === false) {
            throw new Exception('bin: ' . $path . ' not found');
        }

        if (is_executable($path) === false) {
            throw new Exception('bin: not executable');
        }
    }

    public function run(): int
    {
      $rc = $this->_run();

      $this->rc = $rc;
      return $rc;
    }

    public function getReturnCode()
    {
      return $this->rc;
    }

    protected function _run(): int
    {
        $cwd = getcwd();

        while (count($this->pendding) > 0) {
            try {
                // Restore default working directory
                if ($cwd !== getcwd()) {
                    chdir($cwd);
                }

                // Retreive next test
                $testDescriptor = array_shift($this->pendding);
                $test = new $testDescriptor['class'];
                $options = $testDescriptor['options'] ?? [];

                // Execute test
                $this->start($test->getTitle());
                $rc = $test->execute($this, $options);
                $this->finish($rc);
                if ($rc !== true) {
                    return 1;
                }
            } catch (Exception $e) {
                $this->error($e->getMessage());
                if ($this->description !== null) {
                    $this->finish(false);
                }
                return 1;
            }
        }

        return 0;
    }
}

