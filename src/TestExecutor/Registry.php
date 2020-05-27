<?php

namespace Toad\TestExecutor;

trait Registry
{
    private $registry = array();

    function getRegistry($key, $default = null)
    {
      return $this->registry[$key] ?? $default;
    }

    function setRegistry($key, $value)
    {
      $this->registry[$key] = $value;
    }

    function clearRegistry($key)
    {
      unset($this->registry[$key]);
    }

    function issetRegistry($key)
    {
      return isset($this->registry[$key]);
    }

    function resetRegistry()
    {
      $this->registry = array();
    }

    function showRegistry($prefixFilter = null)
    {
      $reg = $this->registry;

      if ($prefixFilter !== null) {
        $reg = array_filter($reg, function($key) use ($prefixFilter) {
          return substr($key, 0, strlen($prefixFilter)) === $prefixFilter;
        }, ARRAY_FILTER_USE_KEY);
      }

      $this->infoArray($reg);
    }
}
