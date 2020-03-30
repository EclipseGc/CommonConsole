<?php

use EclipseGc\CommonConsole\CommandCompilerPass;
use EclipseGc\CommonConsole\ServicesFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

$vendorDir = __DIR__ . '/../vendor';

/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader = require "$vendorDir/autoload.php";
// Collect all the installed
$locations = [__DIR__ . '/..'];
$installed = file_get_contents("$vendorDir/composer/installed.json");
if ($installed) {
  $installed = json_decode($installed);
  foreach ($installed as $package) {
    if (file_exists("$vendorDir/{$package->name}")) {
      $locations[] = "$vendorDir/{$package->name}";
    }
  }
}
$container = new ContainerBuilder();
$loader = new ServicesFileLoader($container, new FileLocator($locations));
$loader->load('services.yml');
$container->addCompilerPass(new RegisterListenersPass());
$container->addCompilerPass(new CommandCompilerPass());
$container->compile();

return $container;
