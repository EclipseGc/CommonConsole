<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceRegistry {

  /**
   * @var \Symfony\Component\DependencyInjection\ContainerBuilder
   */
  private static $container;

  public static function get(string $name): ?object {
    return self::getContainer()->get($name);
  }

  /**
   * Returns the service container.
   */
  protected static function getContainer(): ContainerBuilder {
    if (self::$container === NULL) {
      self::$container = require realpath(__DIR__ . '/../includes/container.php');
    }
    return self::$container;
  }

}
