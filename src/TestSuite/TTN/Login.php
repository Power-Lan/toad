<?php

namespace Toad\TestSuite\TTN;
use Toad;

class Login implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'TTN Login using API Token';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $configurationOk = true;
        $bin = $context->getBin('ttn-lw-cli');

        $region = $context->getConf('ttn.api.region');
        if ($region === null) {
            $context->info('ttn-lw-cli: missing configuration ttn.api.region');
            $configurationOk = false;
        }

        $key = $context->getConf('ttn.api.key');
        if ($region === null) {
            $context->info('ttn-lw-cli: missing configuration ttn.api.key');
            $configurationOk = false;
        }

        if ($configurationOk == false) {
            return false;
        }

        $rc = $context->execute("$bin use --overwrite $region 2>&1");
        if ($rc !== 0) {
            $context->info('ttn-lw-cli: command failed');
            return false;
        }

        $rc = $context->execute("$bin login --api-key $key 2>&1");
        if ($rc !== 0) {
            $context->info('ttn-lw-cli: command failed');
            return false;
        }

        return true;
    }
}
