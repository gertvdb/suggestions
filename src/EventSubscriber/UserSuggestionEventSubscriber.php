<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\suggestions\Service\SuggestionBuilder;
use Drupal\suggestions\Service\SuggestionCleaner;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UserSuggestionEventSubscriber.
 */
class UserSuggestionEventSubscriber implements EventSubscriberInterface {

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

    /** @var User $user */
    $user = $variables['elements']['#user'];

    /** @var string $view_mode */
    $view_mode = $variables['elements']['#view_mode'];

    dump($variables);

    // Add node suggestions
    $suggestions[] = $builder->build([$event->getHook(), $view_mode]);
    $suggestions[] = $builder->build([$event->getHook(), $user->bundle()]);
    $suggestions[] = $builder->build([$event->getHook(), $user->bundle(), $view_mode]);

    //$data_twig_suggestions = isset($element['#attributes']['data-twig-suggestions']) ? $element['#attributes']['data-twig-suggestions'] : [];

    $cleaner->unique($suggestions);
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_user_alter' => 'provideSuggestions',
    ];
  }

}