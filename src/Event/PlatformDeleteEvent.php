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
   * @var bool
   */
  protected $success = FALSE;

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
   * Set the success of the write event.
   *
   * @param bool $value
   */
  public function isSuccessful(bool $value) {
    $this->success = $value;
  }

  /**
   * Whether or not the write was successful. Defaults to false.
   *
   * @return bool
   */
  public function success() : bool {
    return $this->success;
  }

}
