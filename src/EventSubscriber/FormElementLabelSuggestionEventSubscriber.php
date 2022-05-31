<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\preprocess_event_dispatcher\Event\AbstractPreprocessEvent;
use Drupal\preprocess_form_element_event_dispatcher\Event\FormElementPreprocessEvent;
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
      FormElementPreprocessEvent::name() => 'addFormElementType'
    ];
  }

  /**
   * Preprocess form element to add parent type for suggestions to label.
   *
   * This is a small hack until Drupal uses the correct consistent way of theming
   * to add label to the form element. Now we just preprocess the form element and
   * pass the type of the parent along in an array key.
   *
   * @param FormElementPreprocessEvent $event
   *   The event.
   *
   * @return void
   */
  public function addFormElementType(FormElementPreprocessEvent $event): void
  {
    $variables = $event->getVariables();
    $variables->getByReference('label')['type'] = $variables->get('type');
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
    $element = $variables['element'];

    // Small hack to read out parent type. (@see addFormElementType)
    $type = $element['type'] ?? '';

    if ($type) {
      // Add suggestion : form_element_label__TYPE.
      $suggestions[] = $builder->build([$event->getHook(), $type]);
    }

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

    $cleaner->unique($suggestions);
  }
}
