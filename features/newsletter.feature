Feature: Be able to subscribe to newsletter in multiple pages
  In order to receive newsletter
  As an anonymous user
  I need to be able to subscribe to newsletter from Homepage, any article page, ...

  Scenario: In the homepage I should be able to subscribe with all correct informations
    Given I am on "/"
    And I should see "Recevez la newsletter du mouvement"
    When I fill in the following:
      | Adresse email | user-news@newsletter.fr |
      | Code postal   | 75001                   |
      | Pays          | France                  |
    And I press "Je m'inscris"
    Then I should be on "/newsletter/merci"
    And I should see "Vous êtes désormais inscrit à notre newsletter !"

  Scenario: In an article page I should be able to subscribe with all correct informations
    Given the following fixtures are loaded:
      | LoadArticleData |
    And I am on "/articles/actualites/outre-mer"
    And I should see "Recevez la newsletter"
    When I fill in the following:
      | Adresse email | user-news@newsletter.fr |
      | Code postal   | 75001                   |
      | Pays          | France                  |
    And I press "Je m'inscris"
    Then I should be on "/newsletter/merci"
    And I should see "Vous êtes désormais inscrit à notre newsletter !"

  Scenario: In newsletter page I should be able to subscribe with all correct informations
    Given I am on "/newsletter"
    And I should see "Je m'inscris à la newsletter "
    When I fill in the following:
      | Votre adresse e-mail | user-news@newsletter.fr |
      | Votre code postal    | 75001                   |
      | Votre Pays           | France                  |
    And I press "Je m'inscris"
    Then I should be on "/newsletter/merci"
    And I should see "Vous êtes désormais inscrit à notre newsletter !"

  Scenario: In the newsletter page I should have form error if I submit black field
    Given I am on "/newsletter"
    And I should see "Je m'inscris à la newsletter "
    When I press "Je m'inscris"
    Then I should be on "/newsletter"
    And I should see "Cette valeur est requise."
    And I should see "Veuillez renseigner un code postal."
    And I should see "Cette valeur ne doit pas être vide."
    When I fill in the following:
      | Votre adresse e-mail | user-news |
      | Votre code postal    | 75001     |
      | Votre Pays           | France    |
    And I press "Je m'inscris"
    Then I should be on "/newsletter"
    And I should see "Ceci n'est pas une adresse e-mail valide."
