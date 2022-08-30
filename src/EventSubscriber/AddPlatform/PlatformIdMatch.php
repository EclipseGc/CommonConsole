<?php

namespace EclipseGc\CommonConsole\EventSubscriber\AddPlatform;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\AddPlatformToCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Compares platform id to the expectation.
 */
class PlatformIdMatch implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::ADD_PLATFORM_TO_COMMAND]
      = ['onAddPlatformToCommand', 100];
    return $events;
  }

  /**
   * The method called when a platform is added to a command.
   *
   * @param \EclipseGc\CommonConsole\Event\AddPlatformToCommandEvent $event
   *   The Add Platform to Command event.
   */
  public function onAddPlatformToCommand(AddPlatformToCommandEvent $event): void {
    if ($event->getExpectation() === $event->getPlatform()::getPlatformId()) {
      $event->setPlatformExpectationMatch(TRUE);
      $event->stopPropagation();
    }
  }

}
