<?php

namespace Toad\TestSuite;

use Toad;

class Wait implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'Attente';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $msg = $options['msg'] ?? null;
        $delay = $options['delay'] ?? 10;

        if ($msg !== null) {
            $context->info("sleep: $msg");
        }
        $context->info("sleep: $delay");
        sleep($delay);

        return true;
    }
}
