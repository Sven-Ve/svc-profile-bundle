<?php

declare(strict_types=1);


namespace Svc\ProfileBundle\Tests;

require_once(__dir__ . "/Dummy/UserRepositoryDummy.php");
require_once(__dir__ . "/Dummy/UserDummy.php");

use App\Repository\UserRepository;
use App\Security\CustomAuthenticator;
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

class SvcProfileKernel extends Kernel
{
  use MicroKernelTrait;

  private $builder;
  private $routes;
  private $extraBundles;

  /**
   * @param array             $routes  Routes to be added to the container e.g. ['name' => 'path']
   * @param BundleInterface[] $bundles Additional bundles to be registered e.g. [new Bundle()]
   */
  public function __construct(ContainerBuilder $builder = null, array $routes = [], array $bundles = [])
  {
    $this->builder = $builder;
    $this->routes = $routes;
    $this->extraBundles = $bundles;

    parent::__construct('test', true);
  }

  public function registerBundles(): iterable
  {
    return [
      new FrameworkBundle(),
      new SvcProfileBundle(),
      new DoctrineBundle(),
      new SvcUtilBundle(),
      new TwigBundle(),
    ];
  }

  public function registerContainerConfiguration(LoaderInterface $loader): void
  {
    if (null === $this->builder) {
      $this->builder = new ContainerBuilder();
    }

    $builder = $this->builder;

    $loader->load(function (ContainerBuilder $container) use ($builder) {
      $container->merge($builder);

      $container->loadFromExtension(
        'framework',
        [
          'secret' => 'foo',
          'router' => [
            'resource' => 'kernel::loadRoutes',
            'type' => 'service',
            'utf8' => true,
          ],
        ]
      );

      $container->loadFromExtension('doctrine', [
        'dbal' => [
          'driver' => 'pdo_sqlite',
          'url' => 'sqlite:///' . $this->getCacheDir() . '/app.db',
        ],
        'orm' => [
          'auto_generate_proxy_classes' => true,
          'auto_mapping' => true
        ],
      ]);

      $container->register(User::class)
      ->setAutoconfigured(true)
      ->setAutowired(true);

      $container->register(UserRepository::class)
      ->setAutoconfigured(true)
      ->setAutowired(true);

      $container->register(CustomAuthenticator::class)
      ->setAutoconfigured(true)
      ->setAutowired(true);

      $container->register('kernel', static::class)->setPublic(true);

      $kernelDefinition = $container->getDefinition('kernel');
      $kernelDefinition->addTag('routing.route_loader');
    });
  }

  /**
   * load bundle routes
   *
   * @param RoutingConfigurator $routes
   * @return void
   */
  protected function configureRoutes(RoutingConfigurator $routes)
  {
    $routes->import(__DIR__.'/../config/routes.yaml')->prefix('/profile/');
  }

  protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
  {
  }

  /*
    public function getCacheDir()
    {
        return __DIR__.'/../cache/'.spl_object_hash($this);
    }
    */
}
