<?php

namespace Gquemener\UnusedArgument\Rules;

use Gquemener\UnusedArgument\Collector\ArgumentDeclarationCollector;
use Gquemener\UnusedArgument\Collector\ArgumentUsageCollector;
use Gquemener\UnusedArgument\Model\Argument;
use Gquemener\UnusedArgument\Model\Arguments;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;

final class UnusedFunctionArgumentRule implements Rule
{
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $argumentDeclarationData = $node->get(ArgumentDeclarationCollector::class);
        $argumentUseData = $node->get(ArgumentUsageCollector::class);

        $declaredArguments = [];
        foreach ($argumentDeclarationData as $file => $declaration) {
            foreach ($declaration as [$functionName, $arguments, $line]) {
                foreach ($arguments as $argumentName) {
                    $declaredArguments[$functionName.$argumentName] = [$file, $argumentName, $line];
                }
            }
        }

        foreach ($argumentUseData as $usedNamesData) {
            foreach ($usedNamesData as $usedName) {
                unset($declaredArguments[$usedName[0].$usedName[1]]);
            }
        }

        return array_map(
            fn (array $arg): RuleError => 
                RuleErrorBuilder::message(sprintf('$%s argument is defined, but never used in the function body', $arg[1]))
                    ->line($arg[2])
                    ->file($arg[0])
                    ->build(),
            $declaredArguments
        );
    }
}
