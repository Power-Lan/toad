<?php

namespace Toad;

interface TestSuiteInterface
{
    public function getTitle(): string;
    public function execute(TestExecutor $context, array $options): bool;
}
