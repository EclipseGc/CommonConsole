<?php

namespace EclipseGc\CommonConsole;

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait DrupalBootstrapTrait
 *
 * @package EclipseGc\CommonConsole
 */
trait DrupalBootstrapTrait {

  /**
   * Bootstrap Drupal.
   */
  public function bootstrapDrupal() {
    $request = Request::createFromGlobals();
    $kernel = new DrupalKernel('prod', $this->loader, FALSE);
    chdir($kernel->getAppRoot());
    $kernel->handle($request);
  }

}