<?php

namespace EclipseGc\CommonConsole;

use EclipseGc\CommonConsole\Event\OutputFormatterStyleEvent;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class OutputFactory
 *
 * @package EclipseGc\CommonConsole
 */
class OutputFactory {

  /**
   * Creates a new ConsoleOutput object and applies formatter styles.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   *
   * @return \Symfony\Component\Console\Output\ConsoleOutput
   *   The ConsoleOutput with full formatter styling applied.
   */
  public function createOutput(EventDispatcherInterface $dispatcher) {
    $event = new OutputFormatterStyleEvent();
    $dispatcher->dispatch(CommonConsoleEvents::OUTPUT_FORMATTER_STYLE, $event);
    $output = new ConsoleOutput();
    foreach ($event->getFormatterStyles() as $name => $style) {
      if (!$output->getFormatter()->hasStyle($name)) {
        $output->getFormatter()->setStyle($name, $style);
      }
    }
    return $output;
  }

}
