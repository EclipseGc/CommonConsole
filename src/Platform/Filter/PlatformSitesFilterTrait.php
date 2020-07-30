<?php

namespace EclipseGc\CommonConsole\Platform\Filter;

use EclipseGc\CommonConsole\Platform\PlatformSitesInterface;
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
   *
   * @return array
   *   Filtered sites if it was applied any.
   */
  public function getFilteredSites(Command $command): array {
    if (!$this instanceof PlatformSitesInterface) {
      throw new \LogicException('The platform must implement PlatformSitesInterface');
    }

    $sites = $this->getPlatformSites();
    if ($command instanceof PlatformSitesFilterInterface) {
      $command->filter($sites);
    }

    return $sites;
  }

}
