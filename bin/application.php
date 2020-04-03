<?php

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container = include __DIR__ .'/../includes/container.php';
/** @var \Symfony\Component\Console\Application $application */
$application = $container->get('common_console_application');
$application->run();
