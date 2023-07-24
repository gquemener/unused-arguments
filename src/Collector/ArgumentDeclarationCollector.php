<?php

namespace Gquemener\UnusedArgument\Collector;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PhpParser\Node\Param;

final class ArgumentDeclarationCollector implements Collector
{
    public function getNodeType(): string
    {
	return Node\Stmt\Function_::class;
    }

    public function processNode(Node $node, Scope $scope): ?array
    {
	if (null === $node->namespacedName) {
	    return null;
	}

	$arguments = [];
	foreach ($node->params as $param) {
	    $arguments[] = $param->var->name;
	}

	return [$node->namespacedName->toString(), $arguments, $node->getLine()];
    }
}
