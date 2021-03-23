<?php

namespace Svc\ProfileBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SvcProfileExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container)
  {
    $rootPath = $container->getParameter("kernel.project_dir");
    $this->createConfigIfNotExists($rootPath);

    $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.xml');

    $configuration = $this->getConfiguration($configs, $container);
    $config = $this->processConfiguration($configuration, $configs);

    // set arguments for __construct in services
    $definition = $container->getDefinition('svc_profile.controller.change-pw');
    $definition->setArgument(1, $config['enableCaptcha']);

    $definition1 = $container->getDefinition('svc_profile.controller.change-mail');
    $definition1->setArgument(1, $config['enableCaptcha']);
  }

  private function createConfigIfNotExists($rootPath)
  {
    $fileName = $rootPath . "/config/routes/svc_profile.yaml";
    if (!file_exists($fileName)) {
      $text = "_svc_profile:\n";
      $text .= "    resource: '@SvcProfileBundle/src/Resources/config/routes.xml'\n";
      $text .= "    prefix: /svc-profile/{_locale}\n";
      $text .= '    requirements: {"_locale": "%app.supported_locales%"}}\n';
      try {
        file_put_contents($fileName, $text);
        dump("Please adapt config file $fileName");
      } catch (Exception $e) {
        // ignore...
      }
    }

    $fileName = $rootPath . "/config/packages/svc_profile.yaml";
    if (!file_exists($fileName)) {
      $text = "svc_profile:\n";
      $text .= "    # Enable captcha for change email/password forms?\n";
      $text .= "    enableCaptcha: false\n";
      try {
        file_put_contents($fileName, $text);
        dump("Please adapt config file $fileName");
      } catch (Exception $e) {
        // ignore...
      }
    }
  }
}
