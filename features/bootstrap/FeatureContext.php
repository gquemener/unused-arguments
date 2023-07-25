<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Process\Process;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private string $phpFile;

    private array $outputs = [
        'out' => '',
        'err' => '',
    ];

    public function __construct()
    {
        $this->phpFile = tempnam(sys_get_temp_dir(), 'unused-argument');
    }

    /**
     * @Given the following valid PHP:
     */
    public function theFollowingValidPHP(PyStringNode $content): void
    {
        file_put_contents($this->phpFile, (string) $content);

        $process = Process::fromShellCommandline(sprintf('php -l %s', $this->phpFile));
        $output = [];
        $exitCode = $process->run(function($type, $bytes) use (&$output): void {
            $output[] = sprintf('%s: %s', $type, $bytes);
        });

        if (0 !== $exitCode) {
            echo implode('', $output);

            throw new LogicException('File does not contain valid PHP.');
        }
    }

    /**
     * @When PHPStan analyses it
     */
    public function phpstanAnalysesIt()
    {
        $process = Process::fromShellCommandline(sprintf('bin/phpstan analyse %s', $this->phpFile));
        $process->run(function($type, $bytes): void {
            $this->outputs[$type] .= $bytes;
        });
    }

    /**
     * @Then I should see :count violation(s)
     */
    public function iShouldSeeViolation(int|string $count): void
    {
        $pattern = sprintf('Found %d error', $count);

        if ('no' === $count) {
            $pattern = '[OK]';
        }

        if (!str_contains($this->outputs['out'], $pattern)) {
            echo '=== STDOUT ===' . "\n";
            echo $this->outputs['out'] . "\n";
            echo '=== STDERR ===' . "\n";
            echo $this->outputs['err'] . "\n";

            throw new LogicException('A different number of violation(s) was found.');
        }
    }
}
