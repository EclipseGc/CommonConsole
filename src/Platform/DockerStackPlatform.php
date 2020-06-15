<?php

namespace EclipseGc\CommonConsole\Platform;

use Consolidation\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

/**
 * Class DockerStackPlatform.
 *
 * @package EclipseGc\CommonConsole\Platform
 */
class DockerStackPlatform extends PlatformBase implements PlatformSitesInterface {

  /**
   * Services added to the platform.
   */
  public const CONFIG_SERVICES = 'docker.services';

  /**
   * The path to the docker-compose.yml file, from where the command could run.
   */
  public const CONFIG_COMPOSE_FILE_PATH = 'docker.compose_file';

  /**
   * {@inheritdoc}
   */
  public static function getPlatformId(): string {
    return 'DockerStack';
  }

  /**
   * {@inheritdoc}
   */
  public static function getQuestions() {
    return [
      static::CONFIG_SERVICES => new Question("Docker Service Names (spearate them with colon): "),
      static::CONFIG_COMPOSE_FILE_PATH => new Question("Location of docker-compose file: "),
    ];
  }

  /**
   * {@inheritdoc}
   *
   * This implementation relies on the hostname. Overwrite the method for
   * custom site retrieving logic.
   */
  public function getPlatformSites(): array {
    $services = explode(',', $this->get(static::CONFIG_SERVICES));
    $location = $this->get(static::CONFIG_COMPOSE_FILE_PATH);
    $sites = [];
    foreach ($services as $service) {
      $uriProcess = Process::fromShellCommandline("cd $location; docker-compose config | grep -A 11 $service | grep hostname | grep -v database | awk '{print $2}'");
      $uriProcess->run();
      $uri = trim($uriProcess->getOutput());
      $sites[trim($service)] = $uri;
    }

    return $sites;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(Command $command, InputInterface $input, OutputInterface $output) : void {
    $location = $this->get(static::CONFIG_COMPOSE_FILE_PATH);
    $services = explode(',', $this->get(static::CONFIG_SERVICES));
    $sites = $this->getPlatformSites();
    foreach ($services as $service) {
      $output->writeln("Executed on '$service'");
      $process = Process::fromShellCommandline("cd $location; docker-compose exec -T $service ./vendor/bin/commoncli --uri {$sites[$service]} {$input->__toString()}");
      $this->runner->run($process, $this, $output);
    }
  }

}
