<?php

namespace Drupal\suggestions\Event;

use Drupal\hook_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\suggestions\Utility\SuggestionBuilder;

/**
 * Class ExampleFormEventSubscribers.
 */
class NodeSuggestionEventSubscriber implements EventSubscriberInterface {

  /**
   * Provide node suggestions.
   *
   * @param \Drupal\hook_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent $event
   *   The event.
   */
  public function provideNodeSuggestions(ThemeSuggestionsAlterIdEvent $event) {
    $variables = &$event->getVariables();
    $suggestions = &$event->getSuggestions();

    return $suggestions;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_node_alter' => 'provideNodeSuggestions',
    ];
  }

}
