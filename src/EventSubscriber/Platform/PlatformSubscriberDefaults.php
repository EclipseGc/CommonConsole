<?php

namespace EclipseGc\CommonConsole\EventSubscriber\Platform;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;
use EclipseGc\CommonConsole\Event\GetPlatformTypesEvent;
use EclipseGc\CommonConsole\Platform\DdevPlatform;
use EclipseGc\CommonConsole\Platform\DockerStackPlatform;
use EclipseGc\CommonConsole\Platform\SshPlatform;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * PlatformSubscriberDefaults.
 */
class PlatformSubscriberDefaults implements EventSubscriberInterface {

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::GET_PLATFORM_TYPES] = 'onGetPlatformTypes';
    $events[CommonConsoleEvents::GET_PLATFORM_TYPE] = 'onGetPlatformType';
    return $events;
  }

  /**
   * Adds SSH & DDev platforms to allowed platform types.
   */
  public function onGetPlatformTypes(GetPlatformTypesEvent $event): void {
    $event->addPlatformType(SshPlatform::getPlatformId());
    $event->addPlatformType(DdevPlatform::getPlatformId());
    $event->addPlatformType(DockerStackPlatform::getPlatformId());
  }

  /**
   * Adds SSH & Ddev platforms on platform load.
   *
   * @throws \Exception
   */
  public function onGetPlatformType(GetPlatformTypeEvent $event): void {
    if ($event->getPlatformType() === SshPlatform::getPlatformId()) {
      $event->addClass(SshPlatform::class);
      $event->stopPropagation();
      return;
    }
    if ($event->getPlatformType() === DdevPlatform::getPlatformId()) {
      $event->addClass(DdevPlatform::class);
      $event->stopPropagation();
    }

    if ($event->getPlatformType() === DockerStackPlatform::getPlatformId()) {
      $event->addClass(DockerStackPlatform::class);
      $event->stopPropagation();
    }
  }

}
