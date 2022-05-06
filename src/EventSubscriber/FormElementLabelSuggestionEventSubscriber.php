<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\preprocess_event_dispatcher\Event\AbstractPreprocessEvent;
use Drupal\suggestions\Service\SuggestionBuilder;
use Drupal\suggestions\Service\SuggestionCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FormElementLabelSuggestionEventSubscriber.
 */
class FormElementLabelSuggestionEventSubscriber implements EventSubscriberInterface
{

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_form_element_label_alter' => 'provideSuggestions',
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
    /*

    // Add suggestion : form_element_label__TYPE.
    $suggestions[] = $builder->build([$event->getHook(), $element['#type']]);

    // Add suggestion based on extra attribute on input so we can provide an
    // form_element based template suggestion. This should only be used for
    // specific fields that require an other theming than the normal form_element
    // of the type.
    $customTwigSuggestions = isset($element['#attributes']['data-twig-suggestions']) ? $element['#attributes']['data-twig-suggestions'] : [];
    if (!empty($customTwigSuggestions)) {
      foreach ($customTwigSuggestions as $customTwigSuggestion) {
        // Add suggestion : form_element_label__DATA_TWIG_SUGGESTION.
        $suggestions[] = $builder->build([$event->getHook(), $customTwigSuggestion]);
       }
    }
    */
    $cleaner->unique($suggestions);
  }
}
