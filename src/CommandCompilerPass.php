<?php

namespace EclipseGc\CommonConsole;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass for commonconsole commands.
 */
class CommandCompilerPass implements CompilerPassInterface {

  public const SERVICE_NAME = 'common_console_application';

  /**
   * {@inheritDoc}
   */
  public function process(ContainerBuilder $container) {
    if (!$container->has(self::SERVICE_NAME)) {
      return;
    }

    $definition = $container->findDefinition(
      self::SERVICE_NAME
    );
    $taggedServices = $container->findTaggedServiceIds(
      'common_console_command'
    );
    foreach ($taggedServices as $id => $tags) {
      $definition->addMethodCall(
        'add',
        [new Reference($id)]
      );
    }
  }

}
