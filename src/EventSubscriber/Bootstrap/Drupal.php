<?php

namespace EclipseGc\CommonConsole\EventSubscriber\Bootstrap;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use EclipseGc\CommonConsole\CommonConsoleEvents;
use EclipseGc\CommonConsole\Event\BootstrapEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Drupal
 *
 * Used in the context of a Drupal site instance to bootstrap Drupal to a
 * functional state.
 *
 * @package EclipseGc\CommonConsole\EventSubscriber\Bootstrap
 */
class Drupal implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CommonConsoleEvents::PLATFORM_BOOTSTRAP] = 'onPlatformBootstrap';
    return $events;
  }

  /**
   * Identifies drupal8 platforms and attempts to bootstrap them.
   *
   * @param \EclipseGc\CommonConsole\Event\BootstrapEvent $bootstrapEvent
   *   The bootstrap event.
   */
  public function onPlatformBootstrap(BootstrapEvent $bootstrapEvent) {
    if ($bootstrapEvent->getPlatformType() === 'drupal8') {
      if (!class_exists(DrupalKernel::class)) {
        $consoleEvent = $bootstrapEvent->getConsoleCommandEvent();
        $consoleEvent->getOutput()->writeln("<warning>No Drupal installation was found. This command must be run against a Drupal installation.</warning>");
        $consoleEvent->disableCommand();
        return;
      }
      // Set up environment
      $request = Request::createFromGlobals();
      $kernel = new DrupalKernel('prod', $this->loader, FALSE);
      chdir($kernel->getAppRoot());
      $kernel::bootEnvironment();
      $kernel->setSitePath($kernel::findSitePath($request));
      Settings::initialize($kernel->getAppRoot(), $kernel->getSitePath(), $this->loader);
      $kernel->boot();
      $kernel->getContainer()
        ->get('request_stack')
        ->push($request);
    }
  }

}
