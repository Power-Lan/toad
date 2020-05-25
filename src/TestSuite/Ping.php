<?php

namespace Toad\TestSuite;

use Toad;

class Ping implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'Ping';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $ping = $context->getBin('ping');
        $ip = $options['ip'];
        $cmd = $ping . ' -c1 -W1 ' . $ip;
        $timeout = $options['timeout'] ?? 30;

        $context->info("ping: checking $ip with timeout $timeout");
        while ($timeout) {
            $rc = $context->execute($cmd);
            if ($rc === 0) {
                $context->info("ping: success");
                return true;
            }

            $timeout--;
        }

        $context->error("ping: timeout");
        return false;
    }
}
