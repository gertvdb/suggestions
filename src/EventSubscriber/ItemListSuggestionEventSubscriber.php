<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\suggestions\Service\SuggestionBuilder;
use Drupal\suggestions\Service\SuggestionCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ItemListSuggestionEventSubscriber.
 */
class ItemListSuggestionEventSubscriber implements EventSubscriberInterface
{

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_item_list_alter' => 'provideSuggestions',
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
  public function provideSuggestions(ThemeSuggestionsAlterIdEvent $event): void
  {

    $suggestions = &$event->getSuggestions();

    /** @var SuggestionCleaner $cleaner */
    $cleaner = \Drupal::service('suggestions.cleaner');
    $cleaner->clean($suggestions);

    /** @var SuggestionBuilder $cleaner */
    $builder = \Drupal::service('suggestions.builder');

    $variables = $event->getVariables();
    $context = $variables['context']['list_style'];

    if (!empty($context)) {
      // Add suggestion : item_list__context.
      $suggestions[] = $builder->build([$event->getHook(), $context]);
    }

    // Add suggestion based on extra attribute on input so we can provide an
    // form_element based template suggestion. This should only be used for
    // specific fields that require an other theming than the normal form_element
    // of the type.
    $customTwigSuggestions = $element['#attributes']['data-twig-suggestions'] ?? [];
    if (!empty($customTwigSuggestions)) {
      foreach ($customTwigSuggestions as $customTwigSuggestion) {
        // Add suggestion : form_element_label__DATA_TWIG_SUGGESTION.
        $suggestions[] = $builder->build([$event->getHook(), $customTwigSuggestion]);
      }
    }

    $cleaner->unique($suggestions);
  }
}
