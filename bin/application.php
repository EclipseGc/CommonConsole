<?php

// Autoload mechanism stolen from Drush. Clearly they had the same problem.
$cwd = isset($_SERVER['PWD']) && is_dir($_SERVER['PWD']) ? $_SERVER['PWD'] : getcwd();

if (file_exists($autoloadFile = __DIR__ . '/../vendor/autoload.php')
  || file_exists($autoloadFile = __DIR__ . '/../../autoload.php')
  || file_exists($autoloadFile = __DIR__ . '/../../../autoload.php')
) {
  $loader = include_once($autoloadFile);
} else {
  throw new \Exception("Could not locate autoload.php. cwd is $cwd; __DIR__ is " . __DIR__);
}

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container = include __DIR__ .'/../includes/container.php';
/** @var \Symfony\Component\Console\Application $application */
$application = $container->get('common_console_application');
$application->run();
