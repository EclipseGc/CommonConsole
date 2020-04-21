<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

interface PlatformInterface {

  /**
   * Return an array of \Symfony\Component\Console\Question\Question objects.
   *
   * The array of Question objects can be represented either directly as
   * Questions or as a callback to another public static method which evaluates
   * the values and returns a question.
   *
   * Example:
   *  public static function getPlatformQuestions() {
   *    return [
   *      'ssh_user' => new Question("SSH Username: "),
   *      'ssh_url' => new Question("SSH URL: "),
   *      'ssh_remote_dir' => new Question("SSH remote directory: "),
   *      'ssh_remote_vendor_dir' => [SshPlatform::class, 'getRemoteVendorDir'],
   *    ];
   *  }
   *
   *  public static function getRemoteVendorDir(array $values) {...
   *
   * @return array
   */
  public static function getPlatformQuestions();

  /**
   * Execute a command on the defined platform.
   *
   * This should perform the necessary steps to execute the command remotely on
   * the defined platform. They could be done, for example, by passing commands
   * inline across an ssh connections.
   *
   * Platforms are given the ProcessRunner as part of their instantiation. It
   * is suggested that all execute methods use the ProcessRunner to run their
   * processes.
   *
   * @param \Symfony\Component\Console\Command\Command $command
   *   The command to execute.
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   The output interface.
   */
  public function execute(Command $command, OutputInterface $output) : void ;

  /**
   * ProcessRunner proxy method to allow a to response to specific output.
   *
   * @param \Symfony\Component\Process\Process $process
   *   The running process object.
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   The output interface.
   * @param string $type
   *   The type of message from the process object.
   * @param string $buffer
   *   The message from the process object.
   */
  public function out(Process $process, OutputInterface $output, string $type, string $buffer) : void ;

}
