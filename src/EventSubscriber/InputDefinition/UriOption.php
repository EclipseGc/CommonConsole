<?php

namespace EclipseGc\CommonConsole\EventSubscriber\InputDefinition;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\CreateInputEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AliasArgument
 *
 * @package EclipseGc\CommonConsole\EventSubscriber\AddPlatform
 */
class UriOption implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::CREATE_INPUT_DEFINITION] = 'onCreateInputDefinition';
    return $events;
  }

  /**
   * Add alias argument to the input definition.
   *
   * @param \EclipseGc\CommonConsole\Event\CreateInputEvent $event
   *   The create input event.
   */
  public function onCreateInputDefinition(CreateInputEvent $event) {
    // Provide the option for specifying request url.
    $event->getDefinition()->addOption(new InputOption('uri', NULL, InputOption::VALUE_OPTIONAL, 'The url from which to mock a request.'));
  }

}
