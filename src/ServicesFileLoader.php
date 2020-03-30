<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ServicesFileLoader extends YamlFileLoader {

  /**
   * {@inheritdoc}
   */
  public function load($resource, $type = null)
  {
    $paths = $this->locator->locate($resource, NULL, FALSE);
    foreach ($paths as $path) {

      $content = $this->loadFile($path);

      $this->container->fileExists($path);

      // empty file
      if (NULL === $content) {
        return;
      }

      // imports
      $method = new \ReflectionMethod(YamlFileLoader::class, 'parseImports');
      $method->setAccessible(TRUE);
      $method->invoke($this, $content, $path);

      // parameters
      if (isset($content['parameters'])) {
        if (!\is_array($content['parameters'])) {
          throw new InvalidArgumentException(sprintf('The "parameters" key should contain an array in %s. Check your YAML syntax.', $path));
        }

        foreach ($content['parameters'] as $key => $value) {
          $method = new \ReflectionMethod(YamlFileLoader::class, 'resolveServices');
          $method->setAccessible(TRUE);
          $this->container->setParameter($key, $method->invoke($this, $value, $path, TRUE));
        }
      }

      // extensions
      $method = new \ReflectionMethod(YamlFileLoader::class, 'loadFromExtensions');
      $method->setAccessible(TRUE);
      $method->invoke($this, $content);

      // services
      $property = new \ReflectionProperty(YamlFileLoader::class, 'anonymousServicesCount');
      $property->setAccessible(TRUE);
      $property->setValue($this, 0);

      $property = new \ReflectionProperty(YamlFileLoader::class, 'anonymousServicesSuffix');
      $property->setAccessible(TRUE);
      $property->setValue($this,'~' . ContainerBuilder::hash($path));

      $this->setCurrentDir(\dirname($path));
      try {
        $method = new \ReflectionMethod(YamlFileLoader::class, 'parseDefinitions');
        $method->setAccessible(TRUE);
        $method->invoke($this, $content, $path);
      } finally {
        $this->instanceof = [];
        $this->registerAliasesForSinglyImplementedInterfaces();
      }
    }
  }

}