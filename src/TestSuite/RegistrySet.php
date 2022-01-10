<?php

namespace Toad\TestSuite;

use Toad;

class RegistrySet implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'Set registry content';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        foreach($options as $key => $value) {
          $regval = $context->setRegistry($key, $value);
          $context->info("registry: '$key' = '$value'");
        }

        return true;
    }
}
