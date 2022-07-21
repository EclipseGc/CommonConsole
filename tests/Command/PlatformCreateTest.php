<?php

namespace EclipseGc\CommonConsole\Tests\Command;

use Consolidation\Config\Config;
use EclipseGc\CommonConsole\Command\PlatformCreate;
use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;
use EclipseGc\CommonConsole\Event\GetPlatformTypesEvent;
use EclipseGc\CommonConsole\Event\PlatformConfigEvent;
use EclipseGc\CommonConsole\Platform\PlatformFactory;
use EclipseGc\CommonConsole\Platform\PlatformStorage;
use EclipseGc\CommonConsole\PlatformInterface;
use EclipseGc\CommonConsole\QuestionFactory;
use EclipseGc\CommonConsole\Tests\CommonConsoleTestBase;
use EclipseGc\CommonConsole\Tests\Platform\FooPlatform;
use Prophecy\Argument;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PlatformCreateTest extends CommonConsoleTestBase {

  /**
   * Test the PlatformCreate command.
   *
   * @throws \Exception
   */
  public function testPlatformCreate() {
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);

    $dispatcher->dispatch(Argument::type(GetPlatformTypesEvent::class), CommonConsoleEvents::GET_PLATFORM_TYPES)->will(function($arguments) {
      /** @var GetPlatformTypesEvent $event */
      $event = $arguments[0];
      $event->addPlatformType('foo_platform');
      return $event;
    });

    $dispatcher->dispatch(Argument::type(GetPlatformTypeEvent::class), CommonConsoleEvents::GET_PLATFORM_TYPE)->will(function($arguments) {
      /** @var GetPlatformTypeEvent $event */
      $event = $arguments[0];
      $event->addClass(FooPlatform::class);
      return $event;
    });

    $dispatcher->dispatch(Argument::type(PlatformConfigEvent::class), CommonConsoleEvents::PLATFORM_CONFIG)->will(function($arguments) {
      /** @var PlatformConfigEvent $event */
      return $arguments[0];
    });

    $storage = $this->prophesize(PlatformStorage::class);
    $storage->save(Argument::type(PlatformInterface::class))->will(function ($arguments) {
      return $arguments[0];
    })
    ->shouldBeCalledOnce();

    $factory = $this->prophesize(PlatformFactory::class);
    $factory->getMockPlatformFromConfig(Argument::type(Config::class), Argument::type(PlatformStorage::class))->will(function($arguments) {
      /** @var Config $config */
      $config = $arguments[0];
      /** @var PlatformStorage $storage */
      $storage = $arguments[1];
      return new FooPlatform($config->export(), $storage);
    });

    $questionFactory = new QuestionFactory($this->getContainer());

    $command = new PlatformCreate($dispatcher->reveal(), $storage->reveal(), $factory->reveal(), $questionFactory);
    $command->setApplication($this->getContainer()->get('common_console_application'));
    $commandTester = new CommandTester($command);
    $commandTester->setInputs([
      'foo_platform',
      'Foo Test',
      'footest',
    ]);
    $rc = $commandTester->execute(['command' => $command->getName()]);

    $this->assertEquals($this->getExpectedOutput(), $this->normalizeOutput($commandTester->getDisplay()));
    $this->assertEquals(0, $rc);
  }

  /**
   * Normalizes the output removing characters that won't be displayed.
   *
   * Remove unprintable characters and give autocomplete characters
   * representation so that we can compare output.
   *
   * @param string $string
   *
   * @return string|string[]|null
   */
  private function normalizeOutput(string $string) {
    return preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $string);
  }

  /**
   * The expected command output.
   *
   * The autocomplete choice question prints odd characters as part of its
   * output, we need to represent those in our expected output.
   *
   * @return string
   */
  private function getExpectedOutput() {
    $expected = 'This command will step you through the process of creating a new platform on which to perform common console commands.' . PHP_EOL .
'Platform Type:' . PHP_EOL .
'   [0] foo_platform' . PHP_EOL .
' > ' . PHP_EOL .
'Name: Alias: +----------------+--------------+' . PHP_EOL .
'| Property       | Value        |' . PHP_EOL .
'+----------------+--------------+' . PHP_EOL .
'| platform.type  | foo_platform |' . PHP_EOL .
'| platform.name  | Foo Test     |' . PHP_EOL .
'| platform.alias | footest      |' . PHP_EOL .
'+----------------+--------------+' . PHP_EOL .
'Are these config correct? Successfully saved.' . PHP_EOL ;
    return $this->normalizeOutput($expected);
  }

}
