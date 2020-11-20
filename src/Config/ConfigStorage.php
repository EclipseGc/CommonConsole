<?php

namespace EclipseGc\CommonConsole\Config;

use Consolidation\Config\Config;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigStorage
 *
 * @package EclipseGc\CommonConsole\Config
 */
class ConfigStorage {

  /**
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $filesystem;

  /**
   * ConfigStorage constructor.
   *
   * @param \Symfony\Component\Filesystem\Filesystem $filesystem
   */
  public function __construct(Filesystem $filesystem) {
    $this->filesystem = $filesystem;
  }

  public function save(Config $config, string $name, array $dir_parts): void {
    $path = $this->ensureDirectory($dir_parts);
    $config_file = implode(DIRECTORY_SEPARATOR, [$path, "{$name}.yml"]);
    $this->filesystem->dumpFile($config_file, Yaml::dump($config->export(), 7));
  }

  /**
   * Ensure that the config directory exists.
   *
   * @return string
   *   The location of the config directory.
   */
  protected function ensureDirectory(array $dir_parts) : string {
    array_unshift($dir_parts, getenv('HOME'));
    $directory = implode(DIRECTORY_SEPARATOR, $dir_parts);
    if (!$this->filesystem->exists($directory)) {
      $this->filesystem->mkdir($directory);
    }
    return $directory;
  }

  /**
   * Checks config exists or not.
   *
   * @param array $dir
   *   Config location.
   * @param string $name
   *   Config name.
   *
   * @return bool
   */
  public function configExists(array $dir_parts, string $name) : bool {
    $path = $this->ensureDirectory($dir_parts);
    $config_file = implode(DIRECTORY_SEPARATOR, [$path, "{$name}.yml"]);
    return $this->filesystem->exists($config_file);
  }

  /**
   * Returns all available configs within given directory.
   *
   * @return \Consolidation\Config\Config[]
   *   The list of configs.
   */
  public function loadAll(array $dir_parts): array {
    $dir = $this->ensureDirectory($dir_parts);
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
    $files = new \RegexIterator($iterator, '/^.+\.yml$/i', \RecursiveRegexIterator::GET_MATCH);

    $configs = [];
    foreach ($files as $item) {
     foreach ($item as $path) {
       try {
         $content = file_get_contents($path);
         $configs[] = new Config(Yaml::parse($content));
       }
       catch (LogicException $e) {
         $this->logger->error($e->getMessage());
       }
     }
    }

    return $configs;
  }

}
