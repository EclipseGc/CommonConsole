<?php

namespace EclipseGc\CommonConsole\OutputFormatter;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

/**
 * Bare output formatting.
 */
class BareOutputFormatter implements OutputFormatterInterface {

  /**
   * {@inheritDoc}
   */
  public function setDecorated($decorated) {
    // TODO: Implement setDecorated() method.
  }

  /**
   * {@inheritDoc}
   */
  public function isDecorated(): bool {
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function setStyle($name, OutputFormatterStyleInterface $style) {
    // TODO: Implement setStyle() method.
  }

  /**
   * {@inheritDoc}
   */
  public function hasStyle($name): bool {
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function getStyle($name): OutputFormatterStyleInterface {
    throw new InvalidArgumentException(sprintf('Undefined style: "%s".', $name));
  }

  /**
   * {@inheritDoc}
   */
  public function format($message): ?string {
    return $message;
  }

}
