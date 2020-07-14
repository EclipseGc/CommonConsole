<?php

namespace EclipseGc\CommonConsole\Event\Traits;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\PlatformArgumentsEvent;

/**
 * Trait PlatformArgumentsTrait.
 * 
 * Provides helper methods to dispatch the CommonConsoleEvents::PLATFORM_ARGS
 * event.
 * 
 * @package EclipseGc\CommonConsole\Event\Traits
 */
trait PlatformArgumentsTrait {

  /**
   * The event dispatcher service.
   * 
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Returns the decorated input, customized by platform sites.
   * 
   * @param $input
   *   The input object containing the original data. Used as the customization
   *   source.
   * @param $sites
   *   The sites to customize input for.
   * @param $command_name
   *   The name of the current command.
   *
   * @return array
   *   The end result of the mapping.
   */
  protected function dispatchPlatformArgumentsEvent($input, $sites, $command_name): array {
    $event = new PlatformArgumentsEvent($input, $sites, $command_name);
    // Set a default value.
    $event->setDecoratedInput(array_fill_keys($sites, NULL));
    $this->dispatcher->dispatch(CommonConsoleEvents::PLATFORM_ARGS, $event);
    return $event->getDecoratedInput();
  }
  
}
