<?php

namespace Toad\TestExecutor;

trait Asserts
{
    function assertRange($min, $value, $max)
    {
      $msg = sprintf('assert: min=%f value=%f max=%f', $min, $value, $max);

      if ($min !== null) {
          if ($value < $min) {
              $this->error($msg . ' (ERROR)');
              return false;
          }
      }

      if ($max !== null) {
          if ($value > $max) {
              $this->error($msg . ' (ERROR)');
              return false;
          }
      }

      $this->success($msg . ' (OK)');
      return true;
    }
}
