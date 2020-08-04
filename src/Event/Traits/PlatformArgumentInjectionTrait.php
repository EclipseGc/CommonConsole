<?php

namespace EclipseGc\CommonConsole\Event\Traits;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\PlatformArgumentInjectionEvent;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Trait PlatformArgumentInjectionTrait.
 *
 * Provides helper methods to dispatch the CommonConsoleEvents::PLATFORM_ARGS_INJ
 * event.
 *
 * @package EclipseGc\CommonConsole\Event\Traits
 */
trait PlatformArgumentInjectionTrait {

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Returns the decorated input, customized by platform sites.
   *
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   The input object containing the original data. Used as the customization
   *   source.
   * @param array $sites
   *   The sites to customize input for.
   * @param string $command_name
   *   The name of the current command.
   *
   * @return array
   *   The end result of the mapping.
   *
   * @throws \Exception
   */
  protected function dispatchPlatformArgumentInjectionEvent(InputInterface $input, array $sites, string $command_name): array {
    $event = new PlatformArgumentInjectionEvent($input, $sites, $command_name);
    $this->dispatcher->dispatch(CommonConsoleEvents::PLATFORM_ARGS_INJ, $event);
    return $event->getDecoratedInput();
  }
  
}
