Feature: Detect unused method argument
  In order to improve developer experience
  As a developer
  I need to be able to detect method argument that are never used

  Scenario: Successfully detect no error when all method arguments are used
    Given file "Train.php" contains the following valid php:
    """
    <?php

    namespace App\Domain\Model;

    final class Train
    {
      private ?string $nextStation = null;

      public function __construct(
        private readonly int $id
      ) {}

      public function getId(): int
      {
        return $this->id;
      }

      public function setNextStation(string $nextStation): void
      {
        $this->nextStation = $nextStation;
      }

      public function getNextStation(): ?string
      {
        return $this->nextStation;
      }
    }
    """
    When I run phpstan on file "Train.php"
    Then I should see no violation
