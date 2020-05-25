<?php

namespace EclipseGc\CommonConsole\Platform;

use Consolidation\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

/**
 * Class SshPlatform
 *
 * @package EclipseGc\CommonConsole\Platform
 */
class DockerStackPlatform extends PlatformBase {

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
      'docker.services' => new Question("Docker Service Names (spearate them with colon): "),
      'docker.compose_file' => new Question("Location of docker-compose file: "),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function execute(Command $command, InputInterface $input, OutputInterface $output) : void {
    $services = explode(',', $this->get('docker.services'));
    $location = $this->get('docker.compose_file');
    
    foreach ($services as $service) {
      $uriProcess = Process::fromShellCommandline("cd $location; docker-compose config | grep -A 11 $service | grep hostname | grep -v database | awk '{print $2}'");
      $uriProcess->run();
      $uri = trim($uriProcess->getOutput());
      $process = Process::fromShellCommandline("cd $location; docker-compose exec -T $service ./vendor/bin/commoncli --uri $uri {$input->__toString()}");
      $this->runner->run($process, $this, $output);
    }
  }

}
