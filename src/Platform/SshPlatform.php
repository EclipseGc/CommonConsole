<?php

namespace EclipseGc\CommonConsole\Platform;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

/**
 * Class SshPlatform
 *
 * @package EclipseGc\CommonConsole\Platform
 */
class SshPlatform extends PlatformBase {

  /**
   * {@inheritdoc}
   */
  public static function getQuestions() {
    return [
      'ssh_user' => new Question("SSH Username: "),
      'ssh_url' => new Question("SSH URL: "),
      'ssh_remote_dir' => new Question("SSH remote directory: "),
      'ssh_remote_vendor_dir' => [SshPlatform::class, 'getRemoteVendorDir'],
    ];
  }

  /**
   * Creates a vendor directory question based on the remote directory value.
   *
   * @param array $values
   *   The collected values.
   *
   * @return \Symfony\Component\Console\Question\Question
   */
  public static function getRemoteVendorDir(array $values) {
    $parts = [
      $values['ssh_remote_dir'],
      'vendor'
    ];
    $vendor_dir = implode(DIRECTORY_SEPARATOR, $parts);
    return new Question("Remote vendor directory: [$vendor_dir] ", $vendor_dir);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(Command $command, OutputInterface $output) : void {
    $sshUrl = "{$this->config['ssh_user']}@{$this->config['ssh_url']}";
    $process = Process::fromShellCommandline("ssh $sshUrl '.{$this->config['ssh_remote_vendor_dir']}/bin/commoncli {$command->getName()}'");
    $this->runner->run($process, $this);
  }

}
