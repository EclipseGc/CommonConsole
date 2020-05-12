<?php

namespace EclipseGc\CommonConsole\EventSubscriber\ConsoleCommand;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OptionsChecker.
 *
 * @package EclipseGc\CommonConsole\EventSubscriber\ConsoleCommand
 */
class OptionsChecker implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[ConsoleEvents::COMMAND] = ['onConsoleCommand', 150];
    return $events;
  }

  /**
   * Validate commands being executed.
   *
   * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
   *   The console command event.
   *
   * @throws \Exception
   */
  public function onConsoleCommand(ConsoleCommandEvent $event) {
    $this->checkRequiredOptions(
      $event->getCommand()
        ->getDefinition()
        ->getOptions(),
      $event->getInput()
    );
  }

  /**
   * Validates required input options' presence.
   *
   * @param \Symfony\Component\Console\Input\InputOption[] $options
   *   The subjects of validation.
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   The input to validate.
   *
   * @throws \Exception
   */
  protected function checkRequiredOptions(array $options, InputInterface $input): void {
    $missing_options = [];
    foreach ($options as $option) {
      $name = $option->getName();
      if ($option->isValueRequired() && !$input->getOption($name)) {
        $missing_options[] = $name;
      }
    }

    if (empty($missing_options)) {
      return;
    }

    throw new \Exception(sprintf('Missing required option(s): %s', join(', ', $missing_options)));
  }

}
