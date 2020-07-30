<?php

namespace EclipseGc\CommonConsole\Platform\Filter;

use Symfony\Component\Console\Command\Command;

/**
 * Trait PlatformSitesFilterTrait
 * 
 * The trait is supposed to provide a helper method so platform could implement
 * filtering.
 * 
 * @see \EclipseGc\CommonConsole\Platform\Filter\PlatformSitesFilterInterface
 *
 * @package EclipseGc\CommonConsole\Platform\Filter
 */
trait PlatformSitesFilterTrait {

  /**
   * Apply filter in case the command implements the necessary interface.
   * 
   * @param \Symfony\Component\Console\Command\Command $command
   *   The command implementing filter mechanism.
   * @param $sites
   *   Available sites to filter.
   */
  public function applyFiltering(Command $command, &$sites): void {
    if ($command instanceof PlatformSitesFilterInterface) {
      $command->filter($sites);
    }
  }
  
}
