<?php

namespace Drupal\suggestions\Service;

use Exception;
use RuntimeException;

/**
 * Provides a opinionated helpers for build suggestions.
 *
 * @ingroup utility
 */
class SuggestionBuilder {

  /**
   * Delimiter used for suggestion build.
   */
  public const DELIMITER = '__';

  /**
   * Suggestion builder.
   *
   * @param string[] $strings
   *   Add strings as much as you need.
   *
   * @return string
   *   Prepared suggestion string.
   * @throws Exception
   */
  public function build(array $strings): string
  {

    // First sanitize all strings.
    $sanitizedStrings = [];
    foreach ($strings as $string) {

      // Make sure keys are strings.
      if (!is_scalar($string)) {
        throw new RuntimeException("Invalid suggestion array passed to builder : " . implode('|', $strings));
      }

      // Make sure string is lower case.
      $string = mb_strtolower($string);

      // Clean invalid characters.
      $string = $this->removeInvalidChars($string);

      // Limit use of dashes.
      $string = $this->limitDashes($string);

      // Convert the dashes.
      $string = $this->convertDashesToUnderscores($string);

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
  private function removeInvalidChars(string $string): string
  {
    return preg_replace('/[^a-zA-Z0-9-_]/', '', $string);
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
  private function limitDashes(string $string): string
  {
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
  private function convertDashesToUnderscores(string $string): string
  {
    return str_replace('-', '_', $string);
  }

}
