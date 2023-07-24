Feature: Detect unused function argument
  In order to improve developer experience
  As a developer
  I need to be able to detect function argument that are never used

  Scenario: Successfully detect an unused function argument
    Given file "index.php" contains the following valid php:
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
    Given file "index.php" contains the following valid php:
    """
    <?php

    function add(int $a, int $b): int
    {
      return $a + $b;
    }
    """
    When I run phpstan on file "index.php"
    Then I should see no violation

  Scenario: Successfully detect unused argument in namespaced function
    Given file "index.php" contains the following valid php:
    """
    <?php

    namespace Foo {
      function add(int $a, int $b, bool $displayResult): int
      {
        if ($displayResult) echo $a + $b;

        return $a + $b;
      }
    }

    namespace Bar {
      function add(int $a, int $b, bool $displayResult): int
      {
        return $a + $b;
      }
    }
    """
    When I run phpstan on file "index.php"
    Then I should see 1 violation
