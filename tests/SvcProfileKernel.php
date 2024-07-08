<?php

namespace Svc\ProfileBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Svc\ProfileBundle\SvcProfileBundle;
use Svc\UtilBundle\SvcUtilBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Test kernel.
 */
class SvcProfileKernel extends Kernel
{
  use MicroKernelTrait;

  public function registerBundles(): iterable
  {
    yield new FrameworkBundle();
    yield new TwigBundle();
    yield new SvcProfileBundle();
    yield new DoctrineBundle();
    yield new SvcUtilBundle();
  }

  protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
  {
    $config = [
      'http_method_override' => false,
      'secret' => 'foo-secret',
      'test' => true,
    ];

    $container->loadFromExtension('framework', $config);

    $container->loadFromExtension('doctrine', [
      'dbal' => [
        //          'override_url' => true,
        'driver' => 'pdo_sqlite',
        'url' => 'sqlite:///' . $this->getCacheDir() . '/app.db',
      ],
      'orm' => [
        'auto_generate_proxy_classes' => true,
        'auto_mapping' => true,
        'enable_lazy_ghost_objects' => true,
        'report_fields_where_declared' => true,
      ],
    ]);
  }

  /**
   * load bundle routes.
   *
   * @return void
   */
  private function configureRoutes(RoutingConfigurator $routes)
  {
    $routes->import(__DIR__ . '/../config/routes.yaml')->prefix('/profile/');
  }
}
