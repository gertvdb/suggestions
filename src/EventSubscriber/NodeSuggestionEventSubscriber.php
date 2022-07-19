<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\node\Entity\Node;
use Drupal\suggestions\Service\SuggestionBuilder;
use Drupal\suggestions\Service\SuggestionCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class NodeSuggestionEventSubscriber.
 */
class NodeSuggestionEventSubscriber implements EventSubscriberInterface {


  /**
   * Provide suggestions.
   *
   * @param ThemeSuggestionsAlterIdEvent $event
   *   The event.
   *
   * @return void
   */
  public function provideSuggestions(ThemeSuggestionsAlterIdEvent $event) {

    /** @var $variables */
    $variables = $event->getVariables();
    $suggestions = &$event->getSuggestions();

    /** @var SuggestionCleaner $cleaner */
    $cleaner = \Drupal::service('suggestions.cleaner');
    $cleaner->clean($suggestions);

    /** @var SuggestionBuilder $cleaner */
    $builder = \Drupal::service('suggestions.builder');

    /** @var Node $node */
    $node = $variables['elements']['#node'];

    /** @var string $view_mode */
    $view_mode = $variables['elements']['#view_mode'];

    // Add node suggestions
    if (!empty($view_mode)) {
      $suggestions[] = $builder->build([$event->getHook(), $view_mode]);
    }

    if ($node instanceof Node) {
      $suggestions[] = $builder->build([$event->getHook(), $node->bundle()]);
    }

    if (!empty($view_mode) && $node instanceof Node) {
      $suggestions[] = $builder->build([$event->getHook(), $node->bundle(), $view_mode]);
    }

    $cleaner->unique($suggestions);
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_node_alter' => 'provideSuggestions',
    ];
  }

}
