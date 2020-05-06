<?php

namespace EclipseGc\CommonConsole\Event;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\EventDispatcher\Event;

class CreateInputEvent extends Event {

  /**
   * @var \Symfony\Component\Console\Input\InputDefinition
   */
  protected $definition;

  /**
   * CreateInputEvent constructor.
   *
   * @param \Symfony\Component\Console\Input\InputDefinition $definition
   *   The input definition.
   */
  public function __construct(InputDefinition $definition) {
    $this->definition = $definition;
  }

  /**
   * Get the input definition.
   *
   * @return \Symfony\Component\Console\Input\InputDefinition
   */
  public function getDefinition() : InputDefinition {
    return $this->definition;
  }

}
