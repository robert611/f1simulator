Feature: Strona logowania
    Scenario: Wyświetlenie strony logowania
        Given I am on "/login"
        Then I should see "Logowanie"
        Then the response status should be "200"
        Then the page title should be "F1Simulator - Logowanie"