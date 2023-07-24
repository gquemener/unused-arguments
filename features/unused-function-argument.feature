Feature: Detect unused function argument
  In order to improve developer experience
  As a developer
  I need to be able to detect function argument that are never used

  Scenario: Successfully detect an unused function argument
    Given the following "index.php" file:
    """
    <?php

    function add(int $a, int $b, bool $displayResult): int
    {
      return $a + $b;
    }
    """
    And the unused-argument PHPStan extension is enabled 
    When I run phpstan on file "index.php"
    Then I should see 1 violation
