<?php

namespace EclipseGc\CommonConsole\Platform\Filter;

/**
 * Interface PlatformSitesFilterInterface
 *
 * @package EclipseGc\CommonConsole\Platform\Filter
 */
interface PlatformSitesFilterInterface {

  /**
   * Filter sites based on custom condition.
   * 
   * @param array $sites
   *   The available sites to filter.
   */
  public function filter(array &$sites): void;
  
}
