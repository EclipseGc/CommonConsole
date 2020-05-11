<?php

namespace EclipseGc\CommonConsole\Command;

use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\PlatformDeleteEvent;
use EclipseGc\CommonConsole\Platform\PlatformCommandTrait;
use EclipseGc\CommonConsole\PlatformCommandInterface;
use EclipseGc\CommonConsole\PlatformInterface;
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
    $quest = new ConfirmationQuestion(sprintf('Are you certain you want to delete the %s platform? ', $platform->get(PlatformInterface::PLATFORM_NAME_KEY)));
    $answer = $helper->ask($input, $output, $quest);
    if (!$answer) {
      $output->writeln("Delete aborted.");
      return;
    }
    $event = new PlatformDeleteEvent($platform, $output);
    $this->dispatcher->dispatch($event, CommonConsoleEvents::PLATFORM_DELETE);
    if (!$event->hasError()) {
      $output->writeln("Successfully deleted.");
      return;
    }
    $output->writeln(sprintf("<error>The platform was not successfully deleted. The errors encountered were:\n%s</error>", implode("\n", $event->getErrors())));
  }

}
