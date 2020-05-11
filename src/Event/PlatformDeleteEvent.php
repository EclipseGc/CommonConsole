<?php

namespace EclipseGc\CommonConsole\Event;

use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlatformDeleteEvent {

  /**
   * The platform being deleted.
   *
   * @var \EclipseGc\CommonConsole\PlatformInterface
   */
  protected $platform;

  /**
   * The output object.
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  protected $output;

  /**
   * @var array
   */
  protected $errors = [];

  /**
   * PlatformDeleteEvent constructor.
   *
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   *   The platform to delete.
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   The output object.
   */
  public function __construct(PlatformInterface $platform, OutputInterface $output) {
    $this->platform = $platform;
    $this->output = $output;
  }

  /**
   * @return \EclipseGc\CommonConsole\PlatformInterface
   */
  public function getPlatform() : PlatformInterface {
    return $this->platform;
  }

  /**
   * @return \Symfony\Component\Console\Output\OutputInterface
   */
  public function getOutput() : OutputInterface {
    return $this->output;
  }

  /**
   * Adds errors encountered during the event.
   *
   * @param string $error
   *   The specific error message.
   */
  public function addError(string $error) {
    $this->errors[] = $error;
  }

  /**
   * Whether or not any errors were encountered during the event.
   *
   * @return bool
   */
  public function hasError() : bool {
    return (bool) $this->errors;
  }

  /**
   * Gets any logged errors encountered by the event.
   *
   * @return array
   */
  public function getErrors() : array {
    return $this->errors;
  }

}
