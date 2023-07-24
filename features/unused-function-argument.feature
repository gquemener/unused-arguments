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
    When I run phpstan on file "index.php"
    Then I should see 1 violation

  Scenario: Successfully detect no error when all function arguments are used
    Given the following "index.php" file:
    """
    <?php

    function add(int $a, int $b): int
    {
      return $a + $b;
    }
    """
    When I run phpstan on file "index.php"
    Then I should see no violation
