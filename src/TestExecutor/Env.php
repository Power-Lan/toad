<?php

namespace Toad\TestExecutor;

trait Env
{
    function getEnv($key, $default)
    {
      return getenv($key) ?? $default;
    }
}
