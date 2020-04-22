<?php

namespace EclipseGc\CommonConsole\EventSubscriber\FindAlias;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\FindAliasEvent;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;
use EclipseGc\CommonConsole\Event\PlatformWriteEvent;
use EclipseGc\CommonConsole\ProcessRunner;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DefaultFinder
 *
 * @package EclipseGc\CommonConsole\EventSubscriber\FindAlias
 */
class DefaultFinder implements EventSubscriberInterface {

  const PLATFORM_LOCATION = [
    '.commonconsole',
    'platforms',
  ];

  /**
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $filesystem;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * @var \EclipseGc\CommonConsole\ProcessRunner
   */
  protected $runner;

  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * DefaultFinder constructor.
   *
   * @param \Symfony\Component\Filesystem\Filesystem $filesystem
   *   The file system object.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param \EclipseGc\CommonConsole\ProcessRunner $runner
   *   The process runner.
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   */
  public function __construct(Filesystem $filesystem, EventDispatcherInterface $dispatcher, ProcessRunner $runner, ContainerInterface $container) {
    $this->filesystem = $filesystem;
    $this->dispatcher = $dispatcher;
    $this->runner = $runner;
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::ALIAS_FIND] = 'onFindAlias';
    $events[CommonConsoleEvents::PLATFORM_WRITE] = 'onPlatformWrite';
    return $events;
  }

  /**
   * The default platform finder for specific aliases.
   *
   * @param \EclipseGc\CommonConsole\Event\FindAliasEvent $event
   *   The find alias event.
   */
  public function onFindAlias(FindAliasEvent $event) {
    $directory = $this->ensureDirectory();
    $alias_file = implode(DIRECTORY_SEPARATOR, [$directory, "{$event->getAlias()}.yml"]);
    if (!$this->filesystem->exists($alias_file)) {
      return;
    }
    $config = Yaml::parse(file_get_contents($alias_file));
    $platform_event = new GetPlatformTypeEvent($config['platform_type']);
    $this->dispatcher->dispatch(CommonConsoleEvents::GET_PLATFORM_TYPE, $platform_event);
    $event->setPlatform($this->getPlatform($platform_event, $config));
    $event->stopPropagation();;
  }

  /**
   * Instantiates a platform via its factory or through a naive new statement.
   *
   * @param \EclipseGc\CommonConsole\Event\GetPlatformTypeEvent $event
   *   The GetPlatformTypeEvent object.
   * @param array $config
   *   The configuration values to use for instantiating the platform.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   */
  protected function getPlatform(GetPlatformTypeEvent $event, array $config) {
    if ($factory = $event->getFactory()) {
      if ($this->container->has($factory)) {
        return $this->container->get($factory)->create($event, $config, $this->runner);
      }
      if (class_exists($factory)) {
        $factory = new $factory();
        return $factory->create($event, $config, $this->runner);
      }
      throw new LogicException(sprintf("Platform factory service id: %s is missing or undefined.", $factory));
    }
    $class = $event->getClass();
    return new $class($config, $this->runner);
  }

  /**
   * The default platform writer.
   *
   * @param \EclipseGc\CommonConsole\Event\PlatformWriteEvent $event
   *   The platform write event.
   */
  public function onPlatformWrite(PlatformWriteEvent $event) {
    $directory = $this->ensureDirectory();
    $alias_file = implode(DIRECTORY_SEPARATOR, [$directory, "{$event->getAlias()}.yml"]);
    if ($this->filesystem->exists($alias_file)) {
      // @todo ask for a confirmation since we'd be overwriting an existing platform.
    }
    try {
      $this->filesystem->dumpFile($alias_file, Yaml::dump($event->getConfig()));
      $event->isSuccessful(TRUE);
    }
    catch (IOException $e) {}
  }

  /**
   * Ensure that the platform directory exists.
   *
   * @return string
   *   The location of the platform directory.
   */
  protected function ensureDirectory() {
    $dir_parts = static::PLATFORM_LOCATION;
    array_unshift($dir_parts, getenv('HOME'));
    $directory = implode(DIRECTORY_SEPARATOR, $dir_parts);
    if (!$this->filesystem->exists($directory)) {
      $this->filesystem->mkdir($directory);
    }
    return $directory;
  }

}
