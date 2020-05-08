<?php

namespace EclipseGc\CommonConsole\EventSubscriber\FindAlias;

use Consolidation\Config\ConfigInterface;
use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\FindAliasEvent;
use EclipseGc\CommonConsole\Event\PlatformDeleteEvent;
use EclipseGc\CommonConsole\Event\PlatformWriteEvent;
use EclipseGc\CommonConsole\Platform\PlatformStorage;
use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Process\Process;

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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::ALIAS_FIND] = 'onFindAlias';
    $events[CommonConsoleEvents::PLATFORM_WRITE] = 'onPlatformWrite';
    $events[CommonConsoleEvents::PLATFORM_DELETE] = 'onPlatformDelete';
    return $events;
  }

  /**
   * DefaultFinder constructor.
   *
   * @param \EclipseGc\CommonConsole\Platform\PlatformStorage $storage
   *   The platform storage object.
   */
  public function __construct(PlatformStorage $storage) {
    $this->storage = $storage;
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
    // Create a mock platform object to save
    $mock_platform = new class($event->getConfig()) implements PlatformInterface {

      protected $config = [];

      public function __construct(ConfigInterface $config) {
        $this->config = $config;
      }

      public static function getQuestions() {}
      public static function getPlatformId(): string {}
      public function execute(Command $command, InputInterface $input, OutputInterface $output): void {}
      public function out(Process $process, OutputInterface $output, string $type, string $buffer): void {}
      public function get(string $key) {
        return $this->config->get($key);
      }
      public function set(string $key, $value) : self {
        $this->config->set($key, $value);
        return $this;
      }
      public function export() : array {
        return $this->config->export();
      }
    };

    $event->isSuccessful((bool) $this->storage->save($mock_platform));
  }

  /**
   * The default platform delete subscriber.
   *
   * @param \EclipseGc\CommonConsole\Event\PlatformDeleteEvent $event
   *   The platform delete event.
   */
  public function onPlatformDelete(PlatformDeleteEvent $event) {
    $event->isSuccessful($this->storage->delete($event->getPlatform()));
  }

}
