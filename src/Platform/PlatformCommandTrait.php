<?php

namespace EclipseGc\CommonConsole\Platform;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\AddPlatformToCommandEvent;
use EclipseGc\CommonConsole\PlatformInterface;

/**
 * Trait PlatformCommandTrait.
 *
 * @package Eclipse\CommonConsole\Platform
 */
trait PlatformCommandTrait {

  /**
   * Available platforms.
   *
   * @var \EclipseGc\CommonConsole\PlatformInterface[]
   */
  protected $platforms;

  /**
   * Available platform aliases.
   *
   * @var string[]
   */
  protected $platformAliases;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public function addPlatform(string $alias, PlatformInterface $platform): void {
    $options = static::getExpectedPlatformOptions();
    foreach ($options as $name => $expectation) {
      if (empty($this->platforms[$name])) {
        $event = new AddPlatformToCommandEvent($expectation, $platform, $alias);
        $this->dispatcher->dispatch(CommonConsoleEvents::ADD_PLATFORM_TO_COMMAND, $event);
        if (!$event->platformMatchesExpectation()) {
          throw new \Exception(sprintf("Invalid Platform value. Expected a platform of type '%s'. Type of '%s' given.", $expectation, $platform::getPlatformId()));
        }
        $this->platforms[$name] = $platform;
        $this->platformAliases[$name] = $alias;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPlatform(string $name): ?PlatformInterface {
    return $this->platforms[$name] ?? NULL;
  }

}
