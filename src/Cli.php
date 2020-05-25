<?php

namespace Toad;
use Commando;

class Cli
{
  public function run($argc, $argv)
  {
    $cmd = new Commando\Command();

    $cmd->option('scan-tmc')
        ->describedAs('Scan for TMC devices')
        ->boolean();

    $cmd->option('scan-acm')
        ->describedAs('Scan for ACM devices')
        ->boolean();

    $cmd->option('log-directory')
        ->default(getcwd() . '/log')
        ->describedAs('Directory to store log files');

    $cmd->option('run')
        ->describedAs('Execute a test');

    if ($cmd['scan-tmc'] === true) {
      $devices = Device\TMC::scan();
      foreach ($devices as $key => $device) {
        $path = $device['device'];
        $name = $device['name'];
        echo "#$key: $path\t\t$name\n";
      }
      return;
    }

    if ($cmd['scan-acm'] === true) {
      $devices = Device\ACM::scan();
      foreach ($devices as $key => $device) {
        $path = $device['device'];
        echo "#$key: $path\n";
      }
      return;
    }

    if ($cmd['run'] !== null) {
      $config = array(
        'argv'            => $argv,
        'logFolder'       => $cmd['log-directory'],
        'bin'             => array(
          'openocd'       => 'vendor/bin/openocd-stlink2-stm32l0.sh'
        )
      );
      $test = new TestExecutor($config);
      $test->ensureBinaryExists();
      $test->appendTests(array(
        array(
          'class' => $cmd['run'],
          'options' => array()
        )
      ));
      $test->run();
      $test->showFinalStatus();
      return;
    }

    $cmd->printHelp();
  }
}
