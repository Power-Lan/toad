<?php

namespace Toad\TestSuite;

use Toad;

class RegistryCheckPair implements Toad\TestSuiteInterface
{
    public function getTitle(): string
    {
        return 'Check registry content';
    }

    public function execute(Toad\TestExecutor $context, array $options): bool
    {
        $rc = true;

        foreach($options as $key1 => $key2) {
          if ($context->issetRegistry($key1) === false) {
            $rc = false;
            $context->error("registry: $key1 do not exists.");
            continue;
          }

          if ($context->issetRegistry($key2) === false) {
            $rc = false;
            $context->error("registry: $key2 do not exists.");
            continue;
          }

          $regval1 = $context->getRegistry($key1);
          $regval2 = $context->getRegistry($key2);

          if ($regval1 !== $regval2) {
            $rc = false;
            $context->error("registry: '$regval1' is not equals to '$regval2'");
          } else {
            $context->success("registry: '$regval1' is equals to '$regval2'");
          }

        }

        return $rc;
    }
}
