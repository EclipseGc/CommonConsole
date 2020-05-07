<?php

namespace EclipseGc\CommonConsole\Command;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\PlatformDeleteEvent;
use EclipseGc\CommonConsole\Platform\PlatformCommandTrait;
use EclipseGc\CommonConsole\PlatformCommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PlatformDelete
 *
 * @package EclipseGc\CommonConsole\Command
 */
class PlatformDelete extends Command implements PlatformCommandInterface {

  use PlatformCommandTrait;

  /**
   * {@inheritdoc}
   */
  protected static $defaultName = 'platform:delete';

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * PlatformDelete constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param string|NULL $name
   *   The name of this command.
   */
  public function __construct(EventDispatcherInterface $dispatcher, string $name = NULL) {
    parent::__construct($name);
    $this->dispatcher = $dispatcher;
  }

  protected function configure() {
    $this->setDescription('Deletes the specified platform.');
  }


  /**
   * {@inheritdoc}
   */
  public static function getExpectedPlatformOptions(): array {
    return [
      'source' => PlatformCommandInterface::ANY_PLATFORM,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $platform = $this->getPlatform('source');
    $helper = $this->getHelper('question');
    $quest = new ConfirmationQuestion(sprintf('Are you certain you want to delete the %s platform? ', $platform->getConfig()['name']));
    $answer = $helper->ask($input, $output, $quest);
    if ($answer) {
      $event = new PlatformDeleteEvent($platform, $output);
      $this->dispatcher->dispatch($event, CommonConsoleEvents::PLATFORM_DELETE);
      if ($event->success()) {
        $output->writeln("Successfully deleted.");
      }
      else {
        $output->writeln('<error>The platform was not successfully deleted.</error>');
      }
    }
  }

}
