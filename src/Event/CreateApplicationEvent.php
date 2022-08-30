<?php

namespace EclipseGc\CommonConsole\Event;

use Symfony\Component\Console\Application;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * CreateApplicationEvent.
 */
class CreateApplicationEvent extends Event {

  /**
   * Console application.
   *
   * @var \Symfony\Component\Console\Application
   */
  protected $application;

  /**
   * CreateInputEvent constructor.
   *
   * @param \Symfony\Component\Console\Application $application
   *   The application being created.
   */
  public function __construct(Application $application) {
    $this->application = $application;
  }

  /**
   * Returns application.
   *
   * @return \Symfony\Component\Console\Application
   *   Application.
   */
  public function getApplication(): Application {
    return $this->application;
  }

}
