<?php

namespace EclipseGc\CommonConsole;

use EclipseGc\CommonConsole\Event\CreateInputEvent;
use EclipseGc\CommonConsole\Event\OutputFormatterStyleEvent;
use EclipseGc\CommonConsole\OutputFormatter\BareOutputFormatter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class OutputFactory
 *
 * @package EclipseGc\CommonConsole
 */
class IoFactory {

  /**
   * Creates a new ArgvInput object and applies input definition.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param \Symfony\Component\Console\Application $application
   *   The console application.
   *
   * @return \Symfony\Component\Console\Input\ArgvInput
   */
  public static function createInput(EventDispatcherInterface $dispatcher, Application $application) {
    $definition = $application->getDefinition();
    $event = new CreateInputEvent($definition);
    $dispatcher->dispatch(CommonConsoleEvents::CREATE_INPUT_DEFINITION, $event);
    return new ArgvInput(NULL, $event->getDefinition());
  }

  /**
   * Creates a new ConsoleOutput object and applies formatter styles.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   The input object.
   *
   * @return \Symfony\Component\Console\Output\ConsoleOutput
   *   The ConsoleOutput with full formatter styling applied.
   */
  public static function createOutput(EventDispatcherInterface $dispatcher, InputInterface $input) {
    $bare = $input->getOption('bare');
    if ($bare) {
      $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, FALSE, new BareOutputFormatter());
      return $output;
    }
    else {
      $output = new ConsoleOutput();
    }
    $event = new OutputFormatterStyleEvent();
    $dispatcher->dispatch(CommonConsoleEvents::OUTPUT_FORMATTER_STYLE, $event);
    foreach ($event->getFormatterStyles() as $name => $style) {
      if (!$output->getFormatter()->hasStyle($name)) {
        $output->getFormatter()->setStyle($name, $style);
      }
    }
    return $output;
  }

}
