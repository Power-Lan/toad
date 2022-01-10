<?php

namespace Toad\TestSuite\STM32Programmer;
use Toad;

class MCUInfo implements Toad\TestSuiteInterface
{
  public function getTitle(): string
  {
    return 'STM32Programmer > MCUInfo';
  }

  public function execute(Toad\TestExecutor $context, array $options): bool
  {
    $bin = $context->getBin('STM32_Programmer_CLI');

    // Flash size: -r16 0x1FF8007C 1
    // MCU devid: -r16 0x40015800 1
    // MCU revid: -r16 0x40015802 1
    $rc = $context->execute("$bin -c port=SWD freq=8000 mode=NORMAL reset=HWrst -r16 0x1FF8007C 1 -r16 0x40015800 1 -r16 0x40015802 1");
    if ($rc !== 0) {
        return false;
    }
    $content = $context->getLastExecuteOutput();

    $pattern = '/0x([[:xdigit:]]{8})\s+:\s+([[:xdigit:]]+)/';
    $matches = [];
    $rc = preg_match_all($pattern, $content, $matches);
    if ($rc !== 3) {
      return false;
    }

    $extracted = array_combine($matches[1], $matches[2]);
    foreach($extracted as $register => $value) {
      switch($register) {
        case "1FF8007C":
          $context->setRegistry('mcu.flash', hexdec($value));
          break;

        case "40015800":
          $context->setRegistry('mcu.devid', hexdec($value) & 0xFFF);
          break;

        case "40015802":
          $context->setRegistry('mcu.revid', hexdec($value));
          break;

        default:
          break;
      }
    }

    return true;
  }
}