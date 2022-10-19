<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Service file loader across all common-console components.
 */
class ServicesFileLoader extends YamlFileLoader {

  /**
   * Manually invoke private methods on the super class.
   *
   * Many of the methods provided by YamlFileLoader are private, but the public
   * load method only thinks in terms of singular paths found by the locator.
   * We want the locator to retrieve ALL paths that match our criteria. Since
   * the parent class doesn't think in terms of multiple matches, we wrap the
   * existing logic in a foreach loop, and proxy to this method to invoke
   * upstream private calls.
   *
   * @param string $method
   *   Method name.
   * @param mixed $args
   *   Arguments.
   *
   * @return mixed
   *   Method result.
   *
   * @throws \ReflectionException
   */
  protected function getPrivateMethod(string $method, ...$args) {
    $method = new \ReflectionMethod(YamlFileLoader::class, $method);
    $method->setAccessible(TRUE);
    return $method->invoke($this, ...$args);
  }

  /**
   * Manually set private properties on the super class.
   *
   * @param string $property
   *   Property name.
   * @param mixed $value
   *   Property value.
   *
   * @throws \ReflectionException
   */
  protected function getPrivateProperty($property, $value) {
    $property = new \ReflectionProperty(YamlFileLoader::class, $property);
    $property->setAccessible(TRUE);
    $property->setValue($this, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function load(mixed $resource, string $type = NULL): mixed {
    $paths = $this->locator->locate($resource, NULL, FALSE);
    foreach ($paths as $path) {

      $content = $this->loadFile($path);

      $this->container->fileExists($path);

      // Empty file.
      if (NULL === $content) {
        return NULL;
      }

      // Imports.
      $this->getPrivateMethod('parseImports', $content, $path);

      // Parameters.
      if (isset($content['parameters'])) {
        if (!\is_array($content['parameters'])) {
          throw new InvalidArgumentException(sprintf('The "parameters" key should contain an array in %s. Check your YAML syntax.', $path));
        }

        foreach ($content['parameters'] as $key => $value) {
          $this->container->setParameter($key, $this->getPrivateMethod('resolveServices', $value, $path, TRUE));
        }
      }

      // Extensions.
      $this->getPrivateMethod('loadFromExtensions', $content);

      // Services.
      $this->getPrivateProperty('anonymousServicesCount', 0);

      $this->getPrivateProperty('anonymousServicesSuffix', '~' . ContainerBuilder::hash($path));

      $this->setCurrentDir(\dirname($path));
      try {
        $this->getPrivateMethod('parseDefinitions', $content, $path);
      } finally {
        $this->instanceof = [];
        if (method_exists($this, 'registerAliasesForSinglyImplementedInterfaces')) {
          $this->registerAliasesForSinglyImplementedInterfaces();
        }
      }
    }
    return NULL;
  }

}
