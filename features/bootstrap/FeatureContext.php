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
    private string $rootFs;

    private array $outputs = [
        'out' => '',
        'err' => '',
    ];

    public function __construct()
    {
        $this->rootFs = tempnam(sys_get_temp_dir(), 'unused-argument');
        if (file_exists($this->rootFs)) {
            unlink($this->rootFs);
        }
        mkdir($this->rootFs);
    }

    /**
     * @Given the following :filename file:
     */
    public function theFollowingFile(string $filename, PyStringNode $content): void
    {
        file_put_contents($this->rootFs . DIRECTORY_SEPARATOR . $filename, (string) $content);
    }

    /**
     * @Given the unused-argument PHPStan extension is enabled
     */
    public function theUnusedArgumentPhpstanExtensionIsEnabled(): void
    {
    }

    /**
     * @When I run phpstan on file :filename
     */
    public function iRunPhpstanOnFile(string $filename): void
    {
        $process = Process::fromShellCommandline(sprintf('bin/phpstan analyse %s/%s', $this->rootFs, $filename));
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
