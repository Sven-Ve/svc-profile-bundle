<?php

namespace Svc\ProfileBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SvcProfileBundle extends AbstractBundle
{
  public function getPath(): string
  {
    return \dirname(__DIR__);
  }

  public function configure(DefinitionConfigurator $definition): void
  {
    $definition->rootNode()
      ->children()
        ->booleanNode('enableCaptcha')->defaultFalse()->info('Enable captcha for change email/password forms?')->end()
      ->end();
  }

  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
  {
    $container->import('../config/services.yaml');

    $container->services()
      ->get('Svc\ProfileBundle\Controller\ChangeMailController')
      ->arg(0, $config['enableCaptcha']);

    $container->services()
      ->get('Svc\ProfileBundle\Controller\ChangePWController')
      ->arg(0, $config['enableCaptcha']);
  }
}
