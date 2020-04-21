<?php

namespace EclipseGc\CommonConsole\Event;

use Symfony\Component\EventDispatcher\Event;

class PlatformWriteEvent extends Event {

  /**
   * @var array
   */
  protected $config;

  /**
   * @var bool
   */
  protected $success = FALSE;

  /**
   * PlatformWriteEvent constructor.
   *
   * @param array $config
   *   The configuration values to be written.
   */
  public function __construct(array $config) {
    $this->config = $config;
  }

  /**
   * Get the alias string for the platform.
   *
   * @return string
   */
  public function getAlias() : string {
    // @todo be better.
    return $this->config['alias'];
  }

  /**
   * Get the configuration which will be written.
   *
   * @return array
   */
  public function getConfig() : array {
    return $this->config;
  }

  /**
   * Set config values to be saved.
   *
   * @param array $config
   *   The config values to write for the platform.
   */
  public function setConfig(array $config) : void {
    $this->config = $config;
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
