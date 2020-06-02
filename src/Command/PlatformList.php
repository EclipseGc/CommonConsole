<?php

namespace EclipseGc\CommonConsole\Command;

use EclipseGc\CommonConsole\Platform\PlatformStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PlatformList.
 *
 * @package EclipseGc\CommonConsole\Command
 */
class PlatformList extends Command {

  /**
   * {@inheritdoc}
   */
  protected static $defaultName = 'platform:list';

  /**
   * The platform storage.
   *
   * @var \EclipseGc\CommonConsole\Platform\PlatformStorage
   */
  protected $platformStorage;

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setDescription('List available platforms.')
      ->setAliases(['pl']);
  }

  /**
   * PlatformList constructor.
   *
   * @param \EclipseGc\CommonConsole\Platform\PlatformStorage $storage
   *   The platform storage service.
   * @param string|null $name
   *   The name of the command.
   */
  public function __construct(PlatformStorage $storage, string $name = NULL) {
    parent::__construct($name);

    $this->platformStorage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $aliases = $this->loadAll();
    $table = new Table($output);
    $table->setHeaders(['Alias', 'Type']);
    foreach ($aliases as $alias) {
      $platform = $this->platformStorage->load($alias);
      $table->addRow([$alias, $platform->get('platform.type')]);
    }
    $table->render();
  }

  /**
   * Returns all available platforms.
   *
   * @return array
   *   The list of platforms.
   */
  protected function loadAll(): array {
    $dir = join(DIRECTORY_SEPARATOR, array_merge([getenv('HOME')], PlatformStorage::PLATFORM_LOCATION));
    $itarator = new \DirectoryIterator($dir);
    $aliases = [];
    foreach ($itarator as $item) {
      if ($item->getExtension() !== 'yml') {
        continue;
      }
      $file_name = $item->getFilename();
      $aliases[] = mb_substr($file_name, 0, strlen($file_name)-strlen($item->getExtension())-1);
    }
    return $aliases;
  }

}
