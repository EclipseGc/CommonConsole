<?php

namespace EclipseGc\CommonConsole\EventSubscriber\FindAlias;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\FindAliasEvent;
use EclipseGc\CommonConsole\Event\PlatformDeleteEvent;
use EclipseGc\CommonConsole\Event\PlatformWriteEvent;
use EclipseGc\CommonConsole\Platform\PlatformFactory;
use EclipseGc\CommonConsole\Platform\PlatformStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultFinder
 *
 * @package EclipseGc\CommonConsole\EventSubscriber\FindAlias
 */
class DefaultFinder implements EventSubscriberInterface {

  /**
   * @var \EclipseGc\CommonConsole\Platform\PlatformStorage
   */
  protected $storage;

  /**
   * @var \EclipseGc\CommonConsole\Platform\PlatformFactory
   */
  protected $factory;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::ALIAS_FIND] = 'onFindAlias';
    $events[CommonConsoleEvents::PLATFORM_WRITE] = 'onPlatformWrite';
    return $events;
  }

  /**
   * DefaultFinder constructor.
   *
   * @param \EclipseGc\CommonConsole\Platform\PlatformStorage $storage
   *   The platform storage object.
   * @param \EclipseGc\CommonConsole\Platform\PlatformFactory $factory
   *   The platform factory.
   */
  public function __construct(PlatformStorage $storage, PlatformFactory $factory) {
    $this->storage = $storage;
    $this->factory = $factory;
  }

  /**
   * The default platform finder for specific aliases.
   *
   * @param \EclipseGc\CommonConsole\Event\FindAliasEvent $event
   *   The find alias event.
   */
  public function onFindAlias(FindAliasEvent $event) {
    $event->setPlatform($this->storage->load($event->getAlias()));
    $event->stopPropagation();
  }

  /**
   * The default platform writer.
   *
   * @param \EclipseGc\CommonConsole\Event\PlatformWriteEvent $event
   *   The platform write event.
   */
  public function onPlatformWrite(PlatformWriteEvent $event) {
    $mock_platform = $this->factory->getMockPlatformFromConfig($event->getConfig());
    $event->isSuccessful((bool) $this->storage->save($mock_platform));
  }

  /**
   * The default platform delete subscriber.
   *
   * @param \EclipseGc\CommonConsole\Event\PlatformDeleteEvent $event
   *   The platform delete event.
   */
  public function onPlatformDelete(PlatformDeleteEvent $event) {
    try {
      $this->storage->delete($event->getPlatform());
    }
    catch (\Exception $exception) {
      $event->addError($exception->getMessage());
    }
  }

}
