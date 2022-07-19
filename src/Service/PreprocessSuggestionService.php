<?php

namespace Drupal\suggestions\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Class SuggestionService.
 */
class PreprocessSuggestionService {

  public const SUGGESTION_BASE_HOOK = 'theme_suggestions';

  /**
   * Module handler.
   *
   * @var ModuleHandlerInterface
   */
  private ModuleHandlerInterface $moduleHandler;

  /**
   * The theme manager.
   *
   * @var ThemeManagerInterface
   */
  private ThemeManagerInterface $themeManager;

  /**
   * PreprocessEventService constructor.
   *
   * @param ModuleHandlerInterface $moduleHandler
   *   Module handler.
   * @param ThemeManagerInterface $themeManager
   *   The theme manager.
   */
  public function __construct(ModuleHandlerInterface $moduleHandler, ThemeManagerInterface $themeManager) {
    $this->moduleHandler = $moduleHandler;
    $this->themeManager = $themeManager;
  }

  /**
   * Get the hook suggestions.
   *
   * @param string $hook
   *   The hook name.
   * @param array $variables
   *   Variables.
   *
   * @return array
   *   An array of provided suggestions.
   */
  public function getHookSuggestions($hook, array &$variables): array
  {
    $suggestions = $this->moduleHandler->invokeAll(self::SUGGESTION_BASE_HOOK . '_' . $hook, [$variables]);
    $hooks = [
      self::SUGGESTION_BASE_HOOK,
      self::SUGGESTION_BASE_HOOK . '_' . $hook,
    ];
    $this->moduleHandler->alter($hooks, $suggestions, $variables, $hook);
    $this->themeManager->alter($hooks, $suggestions, $variables, $hook);
    return $suggestions;
  }

}
