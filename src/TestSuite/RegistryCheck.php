<?php

namespace Toad\TestSuite;

use Toad;

class RegistryCheck implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'Check registry content';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $rc = true;

        foreach($options as $key => $value) {
          if ($context->issetRegistry($key) === false) {
            $rc = false;
            $context->error("registry: $key do not exists.");
            continue;
          }

          $regval = $context->getRegistry($key);
          if ($regval !== $value) {
            $rc = false;
            $context->error("registry: '$key' = '$regval' is invalid, must be ($value)");
            continue;
          }

          $context->info("registry: '$key' = '$regval' is valid");
        }

        return $rc;
    }
}
