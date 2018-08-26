<?php

namespace Drupal\suggestions\Utility;

/**
 * Provides a opinionated helpers for build suggestions.
 *
 * @ingroup utility
 */
class SuggestionBuilder {

  /**
   * Delimiter used for suggestion build.
   */
  const DELIMITER = '__';

  /**
   * Suggestion builder.
   *
   * @param string[] $strings
   *   Add strings as more as you need.
   *
   * @return string
   *   Prepared suggestion string.
   */
  public static function build(array $strings) {

    // First sanitize all strings.
    $sanitizedStrings = [];
    foreach ($strings as $string) {

      // Make sure keys are strings.
      if (!is_scalar($string)) {
        throw new \Exception("Invalid suggestion array passed to builder.");
      }

      // Make sure string is lower case.
      $string = mb_strtolower($string);

      // Clean invalid characters.
      $string = self::removeInvalidChars($string);

      // Limit use of dashes.
      $string = self::limitDashes($string);

      // Convert the dashes.
      $string = self::convertDashesToUnderscores($string);

      // Fill array with sanitized strings.
      $sanitizedStrings[] = $string;
    }

    return implode(self::DELIMITER, $sanitizedStrings);
  }

  /**
   * Remove all invalid chars.
   *
   * Only letters, numbers and a dash are considered valid characters.
   *
   * @param string $string
   *   The string to clean.
   *
   * @return string
   *   Sanitized string.
   */
  private function removeInvalidChars(string $string) {
    return preg_replace('/[^a-zA-Z0-9-]/', '', $string);
  }

  /**
   * Remove all double dashes.
   *
   * Only single dashes are considered valid.
   *
   * @param string $string
   *   The string to clean.
   *
   * @return string
   *   Sanitized string.
   */
  private static function limitDashes(string $string) {
    return preg_replace('/-+/', '-', $string);
  }

  /**
   * Replace single dashes by underscores.
   *
   * @param string $string
   *   The string to clean.
   *
   * @return string
   *   Sanitized string.
   */
  private static function convertDashesToUnderscores(string $string) {
    return str_replace('-', '_', $string);
  }

}
