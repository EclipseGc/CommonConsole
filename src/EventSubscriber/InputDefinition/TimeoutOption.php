<?php

namespace EclipseGc\CommonConsole\EventSubscriber\InputDefinition;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\CreateApplicationEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TimeoutOption.
 *
 * @package EclipseGc\CommonConsole\EventSubscriber\InputDefinition
 */
class TimeoutOption implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::CREATE_APPLICATION] = 'onCreateApplication';
    return $events;
  }

  /**
   * Add a timeout option.
   *
   * @param \EclipseGc\CommonConsole\Event\CreateApplicationEvent $event
   *   The create input event.
   */
  public function onCreateApplication(CreateApplicationEvent $event) {
    $event->getApplication()->getDefinition()->addOption(new InputOption('timeout', NULL, InputOption::VALUE_REQUIRED, 'The process timeout in seconds.'));
  }

}
