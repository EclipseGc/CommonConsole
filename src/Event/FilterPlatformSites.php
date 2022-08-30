<?php

namespace EclipseGc\CommonConsole\Event;

use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * FilterPlatformSites.
 */
class FilterPlatformSites extends Event {

  /**
   * Command.
   *
   * @var \Symfony\Component\Console\Command\Command
   */
  protected $command;

  /**
   * Platform.
   *
   * @var \EclipseGc\CommonConsole\PlatformInterface
   */
  protected $platform;

  /**
   * Sites list.
   *
   * @var array
   */
  protected $sites;

  /**
   * FilterPlatformSites constructor.
   *
   * @param \Symfony\Component\Console\Command\Command $command
   *   Command.
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   *   Platform.
   * @param array $sites
   *   Sites list.
   */
  public function __construct(Command $command, PlatformInterface $platform, array $sites) {
    $this->command = $command;
    $this->platform = $platform;
    $this->sites = $sites;
  }

  /**
   * Returns command.
   *
   * @return \Symfony\Component\Console\Command\Command
   *   Command.
   */
  public function getCommand(): Command {
    return $this->command;
  }

  /**
   * Returns platform.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   *   Platform.
   */
  public function getPlatform(): PlatformInterface {
    return $this->platform;
  }

  /**
   * Returns sites list.
   *
   * @return array
   *   Site list.
   */
  public function getPlatformSites(): array {
    return $this->sites;
  }

  /**
   * Sets platform sites list.
   *
   * @param array $sites
   *   Sites list.
   */
  public function setPlatformSites(array $sites): void {
    $this->sites = $sites;
  }

}
