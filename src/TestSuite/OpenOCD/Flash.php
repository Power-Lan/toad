<?php

namespace Toad\TestSuite\OpenOCD;

use Toad;

class Flash implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'OpenOCD';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        // pre-exec command
        $preExec = $options['pre-command'] ?? null;
        if ($preExec !== null) {
          $rc = $context->execute($preExec);
          if ($rc !== 0) {
            $context->info('openocd: pre-command command failed');
            return false;
          }
        }

        // OpenOCD Flash
        $firmware = $options['firmware'] ?? null;
        if ($firmware !== null) {
          $openocd = $context->getBin('openocd');
          $rc = $context->execute("$openocd flash $firmware");
          if ($rc !== 0) {
            $context->info('openocd: post-command command failed');
            return false;
          }
        }

        // post-exec command
        $postExec = $options['post-command'] ?? null;
        if ($postExec !== null) {
          $rc = $context->execute($postExec);
          if ($rc !== 0) {
            $context->info('openocd: post-command command failed');
            return false;
          }
        }

        return true;
    }
}
