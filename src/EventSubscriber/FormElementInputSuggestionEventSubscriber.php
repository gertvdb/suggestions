<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\suggestions\Service\SuggestionBuilder;
use Drupal\suggestions\Service\SuggestionCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormElementInputSuggestionEventSubscriber.
 */
class FormElementInputSuggestionEventSubscriber implements EventSubscriberInterface
{

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_input_alter' => 'provideSuggestions',
    ];
  }

  /**
   * Provide suggestions.
   *
   * @param ThemeSuggestionsAlterIdEvent $event
   *   The event.
   *
   * @return void
   */
  public function provideSuggestions(ThemeSuggestionsAlterIdEvent $event) {

    $suggestions = &$event->getSuggestions();

    /** @var SuggestionCleaner $cleaner */
    $cleaner = \Drupal::service('suggestions.cleaner');
    $cleaner->clean($suggestions);

    /** @var SuggestionBuilder $cleaner */
    $builder = \Drupal::service('suggestions.builder');

    $variables = $event->getVariables();

    $element = $variables['element'];

    $type = $element['#type'] ?? NULL;

    // Add suggestion : input__TYPE.
    if (!empty($type)) {
      $suggestions[] = $builder->build([$event->getHook(), $element['#type']]);
    }

    // Add suggestion based on extra attribute on input so we can provide an
    // form_element based template suggestion. This should only be used for
    // specific fields that require an other theming than the normal form_element
    // of the type.
    $customTwigSuggestions = $element['#attributes']['data-twig-suggestions'] ?? [];
    if (!empty($customTwigSuggestions)) {

      if (!is_array($customTwigSuggestions)) {
        throw new \RuntimeException('value passed to data-twig-suggestions should be an array');
      }

      foreach ($customTwigSuggestions as $customTwigSuggestion) {
        // Add suggestion : input__DATA_TWIG_SUGGESTION.
        if (!empty($type)) {
          $suggestions[] = $builder->build([$event->getHook(), $element['#type'], $customTwigSuggestion]);
        }
      }
    }

    $cleaner->unique($suggestions);
  }
}
