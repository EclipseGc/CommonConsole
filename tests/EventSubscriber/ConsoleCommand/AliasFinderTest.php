<?php

namespace EclipseGc\CommonConsole\Tests\EventSubscriber\ConsoleCommand;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class AliasFinderTest.
 *
 * @group eclipsegc_common_console
 *
 * @coversDefaultClass \EclipseGc\CommonConsole\EventSubscriber\ConsoleCommand\AliasFinder
 *
 * @package EclipseGc\CommonConsole\Tests\EventSubscriber\ConsoleCommand
 */
class AliasFinderTest extends TestCase {

  /**
   * The service container used for testing.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerBuilder
   */
  protected static $container;

  /**
   * {@inheritdoc}
   */
  public static function setUpBeforeClass(): void {
    parent::setUpBeforeClass();

    static::$container = require_once __DIR__ .'/../../../includes/container.php';
  }

  /**
   * @covers ::onConsoleCommand
   *
   * @dataProvider appParameterDataProvider
   */
  public function testOnConsoleCommand(array $parameters, string $expected, string $message) {
    /** @var \Composer\Console\Application $application */
    $application = new Application();
    $application->setDispatcher(static::$container->get('event_dispatcher'));
    $application->setAutoExit(FALSE);
    $app_tester = new ApplicationTester($application);

    $app_tester->run($parameters);
    $output = $app_tester->getDisplay();
    $this->assertStringContainsStringIgnoringCase($expected, $output, $message);
  }

  /**
   * Provides parameters for the application under test.
   */
  public function appParameterDataProvider() {
    return [
      [
        ['command' => 'list', '--help' => true],
        'list [options] [--] [<namespace>]',
        'Prints out the usage of list command',
      ],
      [
        ['--help' => true],
        'list [options] [--] [<namespace>]',
        'Prints out the usage of list command',
      ],
      [
        ['command' => 'list', '@foo'],
        'Alias by name foo not found. Please check your available aliases and try again.',
        'There is no such alias.',
      ],
      [
        ['command' => 'list', '--help' => true, '@foo'],
        'Alias by name foo not found. Please check your available aliases and try again.',
        'There is no such alias, if a flag was defined.',
      ],
      [
        ['command' => 'list', '@foo', '--help' => true],
        'Alias by name foo not found. Please check your available aliases and try again.',
        'There is no such alias, no matter where the alias was put.',
      ],
    ];
  }

}
