<?php

namespace Svc\ProfileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder('svc_profile');
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
      ->children()
      ->booleanNode('enableCaptcha')->defaultFalse()->info('Enable captcha for change email/password forms?')->end()
      ->end();
    return $treeBuilder;
  }
}
