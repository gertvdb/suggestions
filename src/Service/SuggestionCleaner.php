<?php

namespace Drupal\suggestions\Service;

/**
 * Provides a cleaner for suggestions.
 *
 * Cleans up suggestions by removing duplicates
 * and id based suggestions.
 *
 * @ingroup utility
 */
class SuggestionCleaner {

  /**
   * remove duplicate from the suggestion list.
   *
   * @param array $suggestions
   */
  public function unique(array &$suggestions): void
  {
    $this->removeDoubleSuggestions($suggestions);
  }

  /**
   * Clean the suggestion list.
   *
   * @param array $suggestions
   */
  public function clean(array &$suggestions): void
  {
    $this->removeUnwantedSuggestions($suggestions);
  }

  /**
   * Remove all suggestions that are unwanted.
   *
   * It's by opinion of this module that all suggestions
   * based on an ID are a bad practice. When a specific entity
   * needs an other template this should be a separate entity
   * type or done in another way than using an ID suggestion.
   *
   * @param $suggestions
   */
  private function removeUnwantedSuggestions(&$suggestions): void
  {
    foreach ($suggestions as $key => $suggestion) {

      // Theming should depend on the id of the entity.
      if (preg_match('/\d/', $suggestion)) {
        unset($suggestions[$key]);
      }

      // Theming should depend on the % wildcard.
      if (str_contains($suggestion, '%')) {
        unset($suggestions[$key]);
      }

      // Theming should depend on whether it's the front page.
      if (str_contains($suggestion, '__front')) {
        unset($suggestions[$key]);
      }

    }
  }

  /**
   * Remove all double suggestions.
   *
   * @param $suggestions
   */
  private function removeDoubleSuggestions(&$suggestions): void
  {
    $suggestions = array_unique($suggestions);
  }

}
