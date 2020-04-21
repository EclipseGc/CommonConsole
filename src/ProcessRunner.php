<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProcessRunner {

  /**
   * The input object.
   *
   * @var \Symfony\Component\Console\Input\InputInterface
   */
  protected $input;

  /**
   * The output object.
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  protected $output;

  /**
   * ProcessRunner constructor.
   *
   * @param \Symfony\Component\Console\Input\InputInterface $input
   *   The input object.
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   The output object.
   */
  public function __construct(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    // @todo there's probably a better way to get OutputStyles set up.
    $warning = new OutputFormatterStyle('black', 'yellow');
    $output->getFormatter()->setStyle('warning', $warning);
    $url = new OutputFormatterStyle('white', 'blue', ['bold']);
    $output->getFormatter()->setStyle('url', $url);
    $this->output = $output;
  }

  /**
   * Run a process, setup a generic callable for processing STDOUT or STDERR.
   *
   * This will also proxy all STDOUT or STDERR data to the Platform which
   * allows the platform object to inspect messages for specific messages to
   * which it might want to react.
   *
   * @param \Symfony\Component\Process\Process $process
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   */
  public function run(Process $process, PlatformInterface $platform) {
    $output = $this->output;
    $process->run(function ($type, $buffer) use ($process, $output, $platform) {
      $platform->out($process, $output, $type, $buffer);
      if (Process::ERR === $type) {
        $output->writeln("<error>$buffer</error>");
      } else {
        $output->writeln("<info>$buffer</info>");
      }
    });
  }

}
