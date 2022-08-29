<?php

namespace EclipseGc\CommonConsole\Event;

use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class PlatformWriteEvent.
 *
 * @package EclipseGc\CommonConsole\Event
 */
class PlatformWriteEvent extends Event {

  /**
   * Platform.
   *
   * @var \EclipseGc\CommonConsole\PlatformInterface
   */
  protected $platform;

  /**
   * Errors.
   *
   * @var array
   */
  protected $errors = [];

  /**
   * PlatformWriteEvent constructor.
   *
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   *   The platform to be written.
   */
  public function __construct(PlatformInterface $platform) {
    $this->platform = $platform;
  }

  /**
   * Get the alias string for the platform.
   *
   * @return string
   *   Alias.
   */
  public function getAlias() : string {
    // @todo be better.
    return $this->platform->getAlias();
  }

  /**
   * Get the platform to be saved.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   *   Platform.
   */
  public function getPlatform() : PlatformInterface {
    return $this->platform;
  }

  /**
   * Adds errors encountered during the event.
   *
   * @param string $error
   *   The specific error message.
   */
  public function addError(string $error) : void {
    $this->errors[] = $error;
  }

  /**
   * Whether any errors were encountered during the event.
   *
   * @return bool
   *   True if errors exist, false otherwise.
   */
  public function hasError() : bool {
    return (bool) $this->errors;
  }

  /**
   * Gets any logged errors encountered by the event.
   *
   * @return array
   *   Errors if any.
   */
  public function getErrors() : array {
    return $this->errors;
  }

}
