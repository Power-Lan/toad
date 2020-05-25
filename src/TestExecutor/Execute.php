<?php

namespace Toad\TestExecutor;

trait Execute
{
    private $executeOutput = array();
    private $executeRc = 0;
    private $executeCmd = null;

    function execute(string $cmd, bool $verbose = true)
    {
      $this->executeOutput = array();
      $this->executeRc = 0;
      $this->executeCmd = $cmd;

      $output = array();
      $rc = -1;
      $this->info("exec: " . $cmd);
      exec($cmd, $output, $rc);
      if ($verbose) {
        $this->infoArray($output, false);
      }
      $this->info("exec: rc=" . $rc);

      $this->executeOutput = $output;
      $this->executeRc = $rc;

      return $rc;
    }

    function getLastExecuteCommand(): string
    {
      return $this->executeCmd;
    }

    function getLastExecuteReturnCode()
    {
      return $this->executeRc;
    }

    function getLastExecuteOutput(): string
    {
      return implode("\n", $this->executeOutput);
    }
}
