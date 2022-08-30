<?php

namespace EclipseGc\CommonConsole\Tests;

use EclipseGc\CommonConsole\OutputFormatter\BareOutputFormatter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * CommonConsoleTestBase base for all tests.
 */
abstract class CommonConsoleTestBase extends TestCase {

  use ProphecyTrait;

  /**
   * The service container used for testing.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected function getContainer(): ContainerBuilder {
    if (empty($this->container)) {
      $this->container = require realpath(__DIR__ . '/../includes/container.php');
    }
    return $this->container;
  }

  /**
   * Get an OutputInterface implementation we can introspect for testing.
   *
   * @return \Symfony\Component\Console\Output\OutputInterface
   *   Output.
   */
  protected function getTestOutput(): OutputInterface {

    $output = new class() implements OutputInterface {

      /**
       * Messages.
       *
       * @var array
       */
      protected $messages = [];

      /**
       * New lines.
       *
       * @var array
       */
      protected $newline = [];

      /**
       * Options.
       *
       * @var array
       */
      protected $options = [];

      /**
       * Returns output.
       */
      public function getOutput() : array {
        return [
          'messages' => $this->messages,
          'newline' => $this->newline,
          'options' => $this->options,
        ];
      }

      /**
       * Writes messages.
       */
      public function write($messages, $newline = FALSE, $options = 0) {
        $count = count($this->messages);
        $this->messages[$count] = $messages;
        $this->newline[$count] = $newline;
        $this->options[$count] = $options;
      }

      /**
       * Writeln override.
       */
      public function writeln($messages, $options = 0) {
        $count = count($this->messages);
        $this->messages[$count] = $messages;
        $this->options[$count] = $options;
      }

      /**
       * Verbosity override.
       */
      public function setVerbosity($level) {}

      /**
       * Get verbosity overide.
       */
      public function getVerbosity() {
        return self::VERBOSITY_QUIET;
      }

      /**
       * isQuiet override.
       */
      public function isQuiet() {
        return TRUE;
      }

      /**
       * isVerbose override.
       */
      public function isVerbose() {
        return FALSE;
      }

      /**
       * isVeryVerbose override.
       */
      public function isVeryVerbose() {
        return FALSE;
      }

      /**
       * isDebug override.
       */
      public function isDebug() {
        return FALSE;
      }

      /**
       * setDecorated override.
       */
      public function setDecorated($decorated) {
        return FALSE;
      }

      /**
       * isDecorated override.
       */
      public function isDecorated() {
        return FALSE;
      }

      /**
       * setFormatter override.
       */
      public function setFormatter(OutputFormatterInterface $formatter) {}

      /**
       * getFormatter override.
       */
      public function getFormatter() {
        return new BareOutputFormatter();
      }

    };
    return $output;
  }

}
