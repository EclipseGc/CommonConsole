<?php

namespace EclipseGc\CommonConsole\EventSubscriber\OutputFormatterStyle;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\OutputFormatterStyleEvent;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Default formatter for output styling.
 */
class DefaultOutputFormatterStyle implements EventSubscriberInterface {

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::OUTPUT_FORMATTER_STYLE] = 'onOutputFormatterStyle';
    return $events;
  }

  /**
   * Adds default formatting to output.
   *
   * @throws \Exception
   */
  public function onOutputFormatterStyle(OutputFormatterStyleEvent $event): void {
    $event->addOutputFormatterStyle('warning', new OutputFormatterStyle('black', 'yellow'));
    $event->addOutputFormatterStyle('url', new OutputFormatterStyle('white', 'blue', ['bold']));
  }

}
