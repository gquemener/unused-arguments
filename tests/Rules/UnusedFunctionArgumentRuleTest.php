<?php

namespace Gquemener\UnusedArgument\Tests\Rules;

use Gquemener\UnusedArgument\Rules\UnusedFunctionArgumentRule;
use Gquemener\UnusedArgument\Collector\ArgumentDeclarationCollector;
use Gquemener\UnusedArgument\Collector\ArgumentUsageCollector;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Rules\Rule;

final class UnusedFunctionArgumentRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new UnusedFunctionArgumentRule();
    }

    protected function getCollectors(): array
    {
        return [
            new ArgumentDeclarationCollector(),
            new ArgumentUsageCollector(),
        ];
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/data/unused-function-argument.php'], [
            [
                '$displayResult argument is defined, but never used in the function body',
                3,
            ],
        ]);
    }
}
