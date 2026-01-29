Feature: Strona logowania
    Scenario: Wyświetlenie strony logowania
        Given I am on "/login"
        Then I should see "Logowanie"