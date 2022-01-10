<?php

namespace Toad\TestExecutor;

trait UserInput
{
    function readFromKeyboard($message) : string
    {
      return readline($message);
    }
}
