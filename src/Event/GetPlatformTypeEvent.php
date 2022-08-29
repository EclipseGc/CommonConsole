<?php

namespace EclipseGc\CommonConsole\Event;

use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GetPlatformTypeEvent.
 *
 * @package EclipseGc\CommonConsole\Event
 */
class GetPlatformTypeEvent extends Event {

  /**
   * Platform type.
   *
   * @var string
   */
  protected $platformType;

  /**
   * Platform class.
   *
   * @var string
   */
  protected $class;

  /**
   * Platform factory.
   *
   * @var string
   */
  protected $factory = '';

  /**
   * GetPlatformTypeEvent constructor.
   *
   * @param string $platformType
   *   Platform type.
   */
  public function __construct(string $platformType) {
    $this->platformType = $platformType;
  }

  /**
   * Get the platform type intended to be instantiated.
   *
   * @return string
   *   Platform type.
   */
  public function getPlatformType() : string {
    return $this->platformType;
  }

  /**
   * Add a class to represent the platform.
   *
   * @param string $class
   *   The class that represents the platform.
   *
   * @throws \Exception
   */
  public function addClass(string $class): void {
    if (!in_array(PlatformInterface::class, class_implements($class))) {
      throw new \Exception(sprintf("Invalid Platform class. Must implement %s.", PlatformInterface::class));
    }
    $this->class = $class;
  }

  /**
   * Get the class to instantiate for the platform.
   *
   * @return string
   *   Platform class.
   */
  public function getClass(): string {
    return $this->class;
  }

}
