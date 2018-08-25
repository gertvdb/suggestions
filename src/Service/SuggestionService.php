<?php

namespace Drupal\suggestions\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Class PreprocessSuggestionService.
 */
class PreprocessSuggestionService {

  const SUGGESTION_BASE_HOOK = 'theme_suggestions';

  /**
   * Module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  private $themeManager;

  /**
   * PreprocessEventService constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module handler.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $themeManager
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
  public function getHookSuggestions($hook, array &$variables) {
    $suggestions = $this->moduleHandler->invokeAll(PreprocessSuggestionService::SUGGESTION_BASE_HOOK . '_' . $hook, [$variables]);
    $hooks = [
      PreprocessSuggestionService::SUGGESTION_BASE_HOOK,
      PreprocessSuggestionService::SUGGESTION_BASE_HOOK . '_' . $hook,
    ];
    $this->moduleHandler->alter($hooks, $suggestions, $variables, $hook);
    $this->themeManager->alter($hooks, $suggestions, $variables, $hook);
    return $suggestions;
  }

}
