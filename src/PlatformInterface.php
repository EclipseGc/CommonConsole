<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

interface PlatformInterface {

  public static function getPlatformQuestions();

  public function execute(Command $command, OutputInterface $output);

}
