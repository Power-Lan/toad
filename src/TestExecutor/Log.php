<?php

namespace Toad\TestExecutor;

use JakubOnderka\PhpConsoleColor\ConsoleColor;

trait Log
{
    private $logColor = null;
    private $logFile = null;
    private $logOutput = array();

    private function __echo($str)
    {
        foreach ($this->logOutput as $value) {
            $str = str_replace('%', '%%', $str);
            fprintf($value, $str);
        }
    }

    public function initLog()
    {
      $this->logColor = new ConsoleColor;

      // STDOUT
      $this->logOutput[] = fopen('php://output', 'w');

      // Log folder
      $folder = $this->getConf('logFolder');

      if (file_exists($folder) === false) {
        mkdir($folder, 0777, true);
      }
      $this->logFile = $folder . '/' . date('Y-m-d_H:i:s') . '.log';
      $this->logOutput[] = fopen($this->logFile, 'w');
    }

    public function closeLog($finalName)
    {
      // Close
        foreach ($this->logOutput as $value) {
            fclose($value);
        }

        $finalName = $this->getConf('logFolder') . '/' . $finalName . '.log';
        rename($this->logFile, $finalName);
    }

    function header(string $description): void
    {
        $this->__echo($this->logColor->apply(array("bg_light_blue", "black"), date('d/m/Y H:i:s') . ' | ' . $description . "\x1B[K") . "\n");
    }

    function headerError(string $description): void
    {
        $this->__echo($this->logColor->apply(array("bg_light_red", "black"), date('d/m/Y H:i:s') . ' | ' . $description . "\x1B[K") . "\n");
    }

    function headerSuccess(string $description): void
    {
        $this->__echo($this->logColor->apply(array("bg_light_green", "black"), date('d/m/Y H:i:s') . ' | ' . $description . "\x1B[K") . "\n");
    }

    function headerWarning(string $description): void
    {
        $this->__echo($this->logColor->apply(array("bg_light_yellow", "black"), date('d/m/Y H:i:s') . ' | ' . $description . "\x1B[K") . "\n");
    }

    function verbose(string $str, bool $newline = true): void
    {
        if (in_array('--verbose', $this->getConf('argv', array()), true)) {
            $this->info($str, $newline);
        }
    }

    function info(string $str, bool $newline = true): void
    {
        if ($newline) {
            $str .= "\n";
        }

        $this->__echo(date('d/m/Y H:i:s') . ' | ' . $str);
    }

    function infoArray($iterable, $showKeys = true): void
    {
        foreach ($iterable as $key => $value) {
            if ($showKeys) {
                $this->info("$key = " . print_r($value, true));
            } else {
                $this->info(print_r($value, true));
            }
        }
    }

    function warning(string $str, bool $newline = true): void
    {
        if ($newline) {
            $str .= "\n";
        }

        $this->__echo($this->logColor->apply("yellow", date('d/m/Y H:i:s') . ' | ' . $str));
    }

    function error(string $str, bool $newline = true): void
    {
        if ($newline) {
            $str .= "\n";
        }

        $this->__echo($this->logColor->apply("red", date('d/m/Y H:i:s') . ' | ' . $str));
    }

    function success(string $str, bool $newline = true): void
    {
        if ($newline) {
            $str .= "\n";
        }

        $this->__echo($this->logColor->apply("green", date('d/m/Y H:i:s') . ' | ' . $str));
    }

    function highlight(string $str, bool $newline = true): void
    {
        if ($newline) {
            $str .= "\n";
        }

        $this->__echo($this->logColor->apply("light_blue", date('d/m/Y H:i:s') . ' | ' . $str));
    }

    function compose(array $log)
    {
        foreach ($log as $l) {
            $this->__echo($this->logColor->apply($l[0], $l[1]));
        }
        $this->__echo("\n");
    }
}
