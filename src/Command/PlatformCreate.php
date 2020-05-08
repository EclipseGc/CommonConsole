<?php

namespace EclipseGc\CommonConsole\Command;

use Consolidation\Config\Config;
use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\GetPlatformTypeEvent;
use EclipseGc\CommonConsole\Event\GetPlatformTypesEvent;
use EclipseGc\CommonConsole\Event\PlatformWriteEvent;
use EclipseGc\CommonConsole\PlatformInterface;
use EclipseGc\CommonConsole\QuestionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CreatePlatform
 *
 * @package EclipseGc\CommonConsole\Command
 */
class PlatformCreate extends Command {

  /**
   * {@inheritdoc}
   */
  protected static $defaultName = 'platform:create';

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * The question factory.
   *
   * @var \EclipseGc\CommonConsole\QuestionFactory
   */
  protected $questionFactory;

  /**
   * CreatePlatform constructor.
   */
  public function __construct(EventDispatcherInterface $dispatcher, QuestionFactory $questionFactory, string $name = NULL) {
    parent::__construct($name);
    $this->dispatcher = $dispatcher;
    $this->questionFactory = $questionFactory;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setDescription('Create a new platform on which to execute common console commands.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('<info>This command will step you through the process of creating a new platform on which to perform common console commands.</info>');
    $table = new Table($output);
    $table->setHeaders(['Property', 'Value']);
    $helper = $this->getHelper('question');
    /** @var \Symfony\Component\Console\Question\Question[] $questions */
    $event = new GetPlatformTypesEvent();
    $this->dispatcher->dispatch(CommonConsoleEvents::GET_PLATFORM_TYPES, $event);
    $question = new ChoiceQuestion("Platform Type: ", $event->getPlatformTypes());
    $platform_type = $helper->ask($input, $output, $question);
    $table->addRow([PlatformInterface::PLATFORM_TYPE_ID, $platform_type]);
    $values = [
      PlatformInterface::PLATFORM_TYPE_ID => $platform_type
    ];

    $questions = [
      PlatformInterface::PLATFORM_NAME => new Question("Name: "),
      PlatformInterface::PLATFORM_ALIAS => new Question("Alias: "),
    ];
    $platform_event = new GetPlatformTypeEvent($platform_type);
    $this->dispatcher->dispatch(CommonConsoleEvents::GET_PLATFORM_TYPE, $platform_event);
    $platform_class = $platform_event->getClass();
    $questions += $platform_class::getQuestions();
    do {
      foreach ($questions as $variable => $question) {
        $question = $this->questionFactory->getQuestion($question, $values);
        $values[$variable] = $helper->ask($input, $output, $question);
        if (is_array($values[$variable])) {
          foreach ($values[$variable] as $value) {
            $table->addRow([$variable, $value]);
          }
        }
        else {
          $table->addRow([$variable, $values[$variable]]);
        }
      }
      $table->render();

      $quest = new ConfirmationQuestion('Are these values correct? ');
      $answer = $helper->ask($input, $output, $quest);
    } while ($answer !== TRUE);
    $write_event = new PlatformWriteEvent($this->getConfigFromValues($values));
    $this->dispatcher->dispatch(CommonConsoleEvents::PLATFORM_WRITE, $write_event);
    if ($write_event->success()) {
      $output->writeln("<info>Platform successfully saved.</info>");
    }
    else {
      $output->writeln("<error>Platform save failed.</error>");
    }
  }

  /**
   * Generates a ConfigInterface object from an array of values.
   *
   * @param array $values
   *   The keys and values from which to generate a Config object.
   *
   * @return \Consolidation\Config\ConfigInterface
   *   The configuration object representing the values.
   */
  protected function getConfigFromValues(array $values) {
    $config = new Config();
    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }
    return $config;
  }

}
