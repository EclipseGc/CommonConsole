<?php

namespace EclipseGc\CommonConsole\Platform;

use Consolidation\Config\ConfigInterface;
use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;
use EclipseGc\CommonConsole\PlatformInterface;
use EclipseGc\CommonConsole\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

/**
 * Class PlatformFactory
 *
 * This class converts Consolidation\Config\ConfigInterface objects into
 * PlatformInterface objects. For saved data, use ::getPlatform() which will
 * load the proper PlatformInterface object with the config data in it.
 *
 * For data that has not yet been saved, use ::getMockPlatformFromConfig().
 * This will create a mock PlatformInterface implementation which can be saved
 * by the PlatformStorage::save() method.
 *
 * @package EclipseGc\CommonConsole\Platform
 */
class PlatformFactory {

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

  public function __construct(EventDispatcherInterface $dispatcher, ProcessRunner $runner, ContainerInterface $container) {
    $this->dispatcher = $dispatcher;
    $this->runner = $runner;
    $this->container = $container;
  }

  /**
   * Instantiates a platform via its factory or through a naive new statement.
   *
   * @param \Consolidation\Config\ConfigInterface $config
   *   The configuration values to use for instantiating the platform.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   */
  public function getPlatform(ConfigInterface $config) : PlatformInterface {
    $event = new GetPlatformTypeEvent($config->get(PlatformInterface::PLATFORM_TYPE_KEY));
    $this->dispatcher->dispatch(CommonConsoleEvents::GET_PLATFORM_TYPE, $event);

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
    if (!$class) {
      throw new LogicException("Platform definition is missing for '{$config->get('platform.alias')}'!");
    }
    
    return new $class($config, $this->runner);
  }

  /**
   * Creates a mock Platform object from provided configuration.
   *
   * PlatformStorage can save mock objects. When it does, it will automatically
   * identify the actual platform type, and instantiate and return the platform
   * class that is appropriate. However since you need a platform to save a
   * platform in the first place, this method provides a handy way of getting a
   * platform object when all you have is a ConfigInterface object.
   *
   * Mostly this is only useful during initial creation of a platform. At all
   * other times, you will have a fully instantiated platform object.
   *
   * @param \Consolidation\Config\ConfigInterface $config
   *   The config for the mock platform.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   */
  public function getMockPlatformFromConfig(ConfigInterface $config) : PlatformInterface {
    // Create a mock platform object to save
    $mock_platform = new class($config) implements PlatformInterface {

      protected $config = [];

      public function __construct(ConfigInterface $config) {
        $this->config = $config;
      }
      public function getAlias(): string {
        return $this->config->get(PlatformInterface::PLATFORM_ALIAS_KEY);
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
    return $mock_platform;
  }

}
