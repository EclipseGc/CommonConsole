<?php

namespace EclipseGc\CommonConsole;

use Consolidation\Config\ConfigInterface;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;

/**
 * Interface PlatformFactoryInterface for instantiation of platform.
 *
 * @package EclipseGc\CommonConsole
 */
interface PlatformFactoryInterface {

  /**
   * Custom instantiation of PlatformInterface objects.
   *
   * PlatformInterface object sometimes require services in order to be
   * instantiated. When this is true, a PlatformFactory facilitates this.
   *
   * @param \EclipseGc\CommonConsole\Event\GetPlatformTypeEvent $event
   *   Event object.
   * @param \Consolidation\Config\ConfigInterface $config
   *   Config object.
   * @param \EclipseGc\CommonConsole\ProcessRunner $runner
   *   Process runner.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   *   Platform.
   */
  public function create(GetPlatformTypeEvent $event, ConfigInterface $config, ProcessRunner $runner) : PlatformInterface;

}
