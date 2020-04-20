<?php

use Symfony\Component\Console\Input\InputArgument;

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container = include __DIR__ .'/../includes/container.php';
/** @var \Symfony\Component\Console\Application $application */
$application = $container->get('common_console_application');
$application->setDispatcher($container->get('event_dispatcher'));
$definition = $application->getDefinition();
// Provide the option for specifying platform aliases.
$definition->addArgument(new InputArgument('alias', InputArgument::OPTIONAL, 'Provide a platform alias for remote execution.'));
$application->run($container->get('console.input'), $container->get('console.output'));
