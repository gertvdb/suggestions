<?php

namespace Drupal\suggestions\EventSubscriber;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent;
use Drupal\suggestions\Service\SuggestionBuilder;
use Drupal\suggestions\Service\SuggestionCleaner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class HtmlEventSubscriber.
 */
class PageSuggestionEventSubscriber implements EventSubscriberInterface
{

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      'hook_event_dispatcher.theme.suggestions_page_alter' => 'provideSuggestions',
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

    /** @var  $request RequestStack */
    $request = \Drupal::service('request_stack');
    $currentRequest = $request->getCurrentRequest();

    /** @var  $all */
    $routeMatch = \Drupal::service('current_route_match');
    $routeName = $routeMatch->getRouteName();

    $all = [];
    if ($currentRequest) {
      $all = $currentRequest->attributes->all();
    }

    // Support older versions when entity was passed through _entity
    $entity = $all['_entity'] ?? NULL;
    if (!$entity && !empty($all)) {
      foreach ($all as $item) {
        if (!$item instanceof ContentEntityInterface) {
          continue;
        }

        $entity = $item;
      }
    }

    /** @var ContentEntityInterface $entity */
    if ($entity) {
      $suggestions[] = $builder->build([$event->getHook(), $entity->getEntityTypeId()]);
      $suggestions[] = $builder->build([$event->getHook(), $entity->getEntityTypeId(), $entity->bundle()]);
    }


    $http_error_suggestions = [
      'system.401' => '401',
      'system.403' => '403',
      'system.404' => '404',
    ];

    if (isset($http_error_suggestions[$routeName])) {
      $suggestions[] = $builder->build([$event->getHook(), '4xx']);
      $suggestions[] = $builder->build([$event->getHook(), $http_error_suggestions[$routeName]]);
    }

    $cleaner->unique($suggestions);
  }
}
