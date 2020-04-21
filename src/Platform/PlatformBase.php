<?php

namespace EclipseGc\CommonConsole\Platform;

use EclipseGc\CommonConsole\PlatformInterface;
use EclipseGc\CommonConsole\ProcessRunner;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class PlatformBase implements PlatformInterface {

  /**
   * @var array
   */
  protected $config;

  /**
   * The process runner.
   *
   * @var \EclipseGc\CommonConsole\ProcessRunner
   */
  protected $runner;

  /**
   * SshPlatform constructor.
   *
   * @param array $config
   *   The configuration values for this platform.
   */
  public function __construct(array $config, ProcessRunner $runner) {
    $this->config = $config;
    $this->runner = $runner;
  }

  public function out(Process $process, OutputInterface $output, string $type, string $buffer) : void {}

}