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

  public static function getPlatformId(): string {
    return 'SSH';
  }


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
      'vendor',
    ];
    $vendor_dir = implode(DIRECTORY_SEPARATOR, $parts);
    return new Question("Remote vendor directory: [$vendor_dir] ", $vendor_dir);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(Command $command, InputInterface $input, OutputInterface $output) : void {
    $sshUrl = "{$this->config['ssh_user']}@{$this->config['ssh_url']}";
    $command_string = $this->runner->getCommandString($input);
//    echo "ssh $sshUrl '.{$this->config['ssh_remote_vendor_dir']}/bin/commoncli $command_string'";
    $process = Process::fromShellCommandline("ssh $sshUrl '.{$this->config['ssh_remote_vendor_dir']}/bin/commoncli $command_string'");
    $this->runner->run($process, $this, $output);
  }

}
