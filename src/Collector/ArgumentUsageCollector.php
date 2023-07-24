<?php

namespace Gquemener\UnusedArgument\Collector;

use Gquemener\UnusedArgument\Model\Argument;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

final class ArgumentUsageCollector implements Collector
{
    public function getNodeType(): string
    {
	return Node\Expr\Variable::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
	return [$scope->getFunctionName(), $node->name];
    }
}
