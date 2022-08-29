<?php

namespace EclipseGc\CommonConsole\Event;

use EclipseGc\CommonConsole\PlatformInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AddPlatformToCommandEvent.
 *
 * @package EclipseGc\CommonConsole\Event
 */
class AddPlatformToCommandEvent extends Event {

  /**
   * The expectation string.
   *
   * This could be a class name, an interface, a simple constant. Any string we
   * can make a comparison against to determine if the platform is valid for
   * the target command.
   *
   * @var string
   */
  protected $expectation;

  /**
   * The incoming platform object.
   *
   * @var \EclipseGc\CommonConsole\PlatformInterface
   */
  protected $platform;

  /**
   * The alias used to load the platform.
   *
   * @var string
   */
  protected $alias;

  /**
   * Whether or not the platform matches the expectation.
   *
   * Defaults to false.
   *
   * @var bool
   */
  protected $platformMatchesExpectation = FALSE;

  /**
   * AddPlatformToCommandEvent constructor.
   *
   * @param string $expectation
   *   Expectation string.
   * @param \EclipseGc\CommonConsole\PlatformInterface $platform
   *   Platform object.
   * @param string $alias
   *   Platform alias.
   */
  public function __construct(string $expectation, PlatformInterface $platform, string $alias) {
    $this->expectation = $expectation;
    $this->platform = $platform;
    $this->alias = $alias;
  }

  /**
   * Returns expectation.
   *
   * @return string
   *   Expectation.
   */
  public function getExpectation(): string {
    return $this->expectation;
  }

  /**
   * Returns platform.
   *
   * @return \EclipseGc\CommonConsole\PlatformInterface
   *   Returns platform.
   */
  public function getPlatform(): PlatformInterface {
    return $this->platform;
  }

  /**
   * Returns alias.
   *
   * @return string
   *   Platform alias.
   */
  public function getAlias(): string {
    return $this->alias;
  }

  /**
   * Sets expectation match flag.
   *
   * @param bool $value
   *   True or false expectation value.
   */
  public function setPlatformExpectationMatch(bool $value): void {
    $this->platformMatchesExpectation = $value;
  }

  /**
   * Returns expectation match.
   *
   * @return bool
   *   Expectation match flag.
   */
  public function platformMatchesExpectation(): bool {
    return $this->platformMatchesExpectation;
  }

}
