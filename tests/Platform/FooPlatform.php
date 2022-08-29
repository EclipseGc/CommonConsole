<?php

namespace EclipseGc\CommonConsole\Tests\Platform;

use EclipseGc\CommonConsole\Platform\PlatformStorage;
use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * FooPlatform for testing platform.
 */
class FooPlatform implements PlatformInterface {

  /**
   * Platform values.
   *
   * @var array
   */
  protected $values;

  /**
   * Platform storage.
   *
   * @var \EclipseGc\CommonConsole\Platform\PlatformStorage
   */
  protected $storage;

  /**
   * FooPlatform constructor.
   *
   * @param array $values
   *   Platform values.
   * @param \EclipseGc\CommonConsole\Platform\PlatformStorage $storage
   *   Platform storage.
   */
  public function __construct(array $values, PlatformStorage $storage) {
    $this->values = $values;
    $this->storage = $storage;
  }

  /**
   * Returns questions.
   *
   * @return array
   *   Questions.
   */
  public static function getQuestions(): array {
    return [];
  }

  /**
   * Returns platform id.
   *
   * @return string
   *   Platform id.
   */
  public static function getPlatformId(): string {
    return 'FOOTEST';
  }

  /**
   * Returns alias.
   *
   * @return string
   *   Alias.
   */
  public function getAlias(): string {
    return $this->get(PlatformInterface::PLATFORM_ALIAS_KEY);
  }

  /**
   * Execute method for foo platform.
   */
  public function execute(Command $command, InputInterface $input, OutputInterface $output): int {
    // TODO: Implement execute() method.
  }

  /**
   * Out method for foo platform.
   */
  public function out(Process $process, OutputInterface $output, string $type, string $buffer): void {
    // TODO: Implement out() method.
  }

  /**
   * Get method.
   */
  public function get(string $key) {
    return $this->values[$key];
  }

  /**
   * Set method.
   */
  public function set(string $key, $value) {
    $this->values[$key] = $value;
  }

  /**
   * Returns export config values.
   */
  public function export(): array {
    return $this->values;
  }

  /**
   * Saves platform.
   */
  public function save(): PlatformInterface {
    return $this->storage->save($this);
  }

}
