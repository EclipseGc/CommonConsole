<?php

namespace EclipseGc\CommonConsole\Platform;

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
class SshPlatform extends PlatformBase {

  /**
   * {@inheritdoc}
   */
  public static function getPlatformId(): string {
    return 'SSH';
  }

  /**
   * {@inheritdoc}
   */
  public static function getQuestions() {
    return [
      'ssh.user' => new Question("SSH Username: "),
      'ssh.url' => new Question("SSH URL: "),
      'ssh.remote_dir' => new Question("SSH remote directory: "),
      'ssh.remote_vendor_dir' => [SshPlatform::class, 'getRemoteVendorDir'],
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
      $values['ssh.remote_dir'],
      'vendor',
    ];
    $vendor_dir = implode(DIRECTORY_SEPARATOR, $parts);
    return new Question("Remote vendor directory: [$vendor_dir] ", $vendor_dir);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(Command $command, InputInterface $input, OutputInterface $output) : void {
    $sshUrl = "{$this->get('ssh.user')}@{$this->get('ssh.url')}";
    $process = Process::fromShellCommandline("ssh $sshUrl '.{$this->get('ssh.remote_vendor_dir')}/bin/commoncli {$input->__toString()}'");
    $this->runner->run($process, $this, $output);
  }

}
