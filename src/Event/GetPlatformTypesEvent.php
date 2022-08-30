<?php

namespace EclipseGc\CommonConsole\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GetPlatformTypesEvent.
 *
 * @package EclipseGc\CommonConsole\Event
 */
class GetPlatformTypesEvent extends Event {

  /**
   * Platform types.
   *
   * @var string[]
   */
  protected $platformTypes;

  /**
   * Add a platform type.
   *
   * @param string $platform_type
   *   The platform type to add.
   */
  public function addPlatformType(string $platform_type): void {
    $this->platformTypes[] = $platform_type;
  }

  /**
   * Get the platform types.
   *
   * @return array
   *   Platform types.
   */
  public function getPlatformTypes() : array {
    return $this->platformTypes;
  }

}
