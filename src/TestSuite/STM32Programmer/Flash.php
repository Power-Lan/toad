<?php

namespace Toad\TestSuite\STM32Programmer;
use Toad;

class Flash implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'STM32Programmer > Flash';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $firmware = $options['firmware'] ?? null;
        if ($firmware === null) {
            $context->info('flash: no firmware filepath');
            return false;
        }

        $bin = $context->getBin('STM32_Programmer_CLI');
        $opts = "-c port=SWD freq=8000 mode=NORMAL reset=HWrst";
        $rc = $context->execute("$bin $opts -d $firmware");
        if ($rc !== 0) {
            $context->info('flash: command failed');
            return false;
        }

        return true;
    }
}
