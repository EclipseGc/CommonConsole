<?php

use EclipseGc\CommonConsole\CommandCompilerPass;
use EclipseGc\CommonConsole\ServicesFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/** @var \Composer\Autoload\ClassLoader $autoloader */
[$autoloader, $autoloadFile] = require __DIR__ . "/autoload.php";
$vendorDir = dirname($autoloadFile);
// Collect all the installed
$locations = [
  __DIR__ . '/..',
  $vendorDir . '/..'
];
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
$loader->load('commoncli.services.yml');
$container->setParameter('autoloader', $autoloader);
$container->setParameter('autoload.file', $autoloadFile);
$container->addCompilerPass(new RegisterListenersPass());
$container->addCompilerPass(new CommandCompilerPass());
$container->compile();

return $container;
