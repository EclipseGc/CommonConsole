<?php

namespace EclipseGc\CommonConsole\Platform;

use EclipseGc\CommonConsole\PlatformCommandInterface;
use EclipseGc\CommonConsole\PlatformInterface;

/**
 * Trait PlatformCommandTrait.
 *
 * @package Eclipse\CommonConsole\Platform
 */
trait PlatformCommandTrait {

  /**
   * Available platforms.
   *
   * @var \EclipseGc\CommonConsole\PlatformInterface[]
   */
  protected $platforms;

  /**
   * Available platform aliases.
   *
   * @var string[]
   */
  protected $platformAliases;

  /**
   * {@inheritdoc}
   */
  public function addPlatform(string $alias, PlatformInterface $platform): void {
    $options = static::getExpectedPlatformOptions();
    foreach ($options as $name => $expectation) {
      if (empty($this->platforms[$name])) {
        if ($expectation !== PlatformCommandInterface::ANY_PLATFORM && $platform::getPlatformId() !== $expectation) {
          throw new \Exception(sprintf("Invalid Platform value. Expected a platform of type '%s'. Type of '%s' given.", $expectation, $platform::getPlatformId()));
        }
        $this->platforms[$name] = $platform;
        $this->platformAliases[$name] = $alias;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPlatform(string $name): ?PlatformInterface {
    return $this->platforms[$name] ?? NULL;
  }

}
