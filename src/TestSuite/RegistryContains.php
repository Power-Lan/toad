<?php

namespace Toad\TestSuite;

use Toad;

class RegistryContains implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'Ensure registry contains some keys';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $rc = true;

        foreach($options as $key) {
          if ($context->issetRegistry($key) === false) {
            $rc = false;
            $context->error("registry: $key do not exists.");
            continue;
          }

          $context->info("registry: '$key' exists");
        }

        return $rc;
    }
}
