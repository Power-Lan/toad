<?php

namespace Toad\TestSuite;

use Toad;

class STInfo implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'ST Info';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        //$bin = $context->getBin('st-info'); // FFS
        // st-info --probe => probe command may return "0 stlink found" and do a success exists
        // st-info --serial => will fail if no µP is connected

        // Show st-info version in the log
        $rc = $context->execute("st-info --version");
        if ($rc !== 0) {
            return false;
        }

        $commands = array(
          'flash',
          'sram',
          'descr',
          'pagesize',
          'chipid',
        );
        foreach ($commands as $command) {
          $rc = $context->execute("st-info --" . $command, false);
          if ($rc !== 0) {
              return false;
          }
          $serial = $context->getLastExecuteOutput();
          $context->setRegistry('mcu.' . $command, $serial);
        }

        $context->info('STM MCU informations');
        $context->showRegistry('mcu.');

        return true;
    }
}
