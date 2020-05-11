<?php

namespace EclipseGc\CommonConsole\Platform;

use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;
use EclipseGc\CommonConsole\PlatformInterface;
use EclipseGc\CommonConsole\ProcessRunner;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class PlatformStorage
 *
 * @package EclipseGc\CommonConsole\Platform
 */
class PlatformStorage {

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
   * Loads a platform object by alias.
   *
   * @param string $alias
   *   The alias of the platform to load.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface|null
   *   The platform object on success. NULL if it doesn't exist.
   */
  public function load(string $alias) : ?PlatformInterface {
    $directory = $this->ensureDirectory();
    $alias_file = implode(DIRECTORY_SEPARATOR, [$directory, "$alias.yml"]);
    if (!$this->filesystem->exists($alias_file)) {
      return NULL;
    }
    $config = new Config(Yaml::parse(file_get_contents($alias_file)));
    $platform_event = new GetPlatformTypeEvent($config->get(PlatformInterface::PLATFORM_TYPE_ID));
    $this->dispatcher->dispatch(CommonConsoleEvents::GET_PLATFORM_TYPE, $platform_event);
    return $this->getPlatform($platform_event, $config);
  }

  /**
   * Checks to see if a specific platform exists.
   *
   * @param string $name
   *   The platform to check to see if it exists.
   *
   * @return bool
   */
  public function exists(string $name) : bool {
    $directory = $this->ensureDirectory();
    $alias_file = implode(DIRECTORY_SEPARATOR, [$directory, "$name.yml"]);
    return $this->filesystem->exists($alias_file);
  }

  /**
   * Save a platform object.
   *
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   *   The platform to save.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface|null
   *   Returns a fully loaded platform object on success. Null on failure.
   */
  public function save(PlatformInterface $platform) : ?PlatformInterface {
    $directory = $this->ensureDirectory();
    $alias = $platform->getAlias();
    $alias_file = implode(DIRECTORY_SEPARATOR, [$directory, "{$alias}.yml"]);
    try {
      $this->filesystem->dumpFile($alias_file, Yaml::dump($platform->export()));
      return $this->load($alias);
    }
    catch (IOException $e) {}
  }

  /**
   * Delete a platform.
   *
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   *   The platform to delete.
   *
   * @return bool|null
   */
  public function delete(PlatformInterface $platform) : ?bool {
    $directory = $this->ensureDirectory();
    $alias_file = implode(DIRECTORY_SEPARATOR, [$directory, "{$platform->getAlias()}.yml"]);
    if (!$this->filesystem->exists($alias_file)) {
      return NULL;
    }
    try {
      $this->filesystem->remove($alias_file);
      return TRUE;
    }
    catch (IOException $e) {}
    return FALSE;
  }

  /**
   * Instantiates a platform via its factory or through a naive new statement.
   *
   * @param \EclipseGc\CommonConsole\Event\GetPlatformTypeEvent $event
   *   The GetPlatformTypeEvent object.
   * @param \Consolidation\Config\ConfigInterface $config
   *   The configuration values to use for instantiating the platform.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   */
  protected function getPlatform(GetPlatformTypeEvent $event, ConfigInterface $config) {
    if ($factory = $event->getFactory()) {
      if ($this->container->has($factory)) {
        // @todo check the factory is an instance of PlatformFactoryInterface.
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
