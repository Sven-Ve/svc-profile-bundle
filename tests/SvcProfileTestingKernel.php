<?php
namespace Svc\ProfileBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Svc\ProfileBundle\SvcProfileBundle;
use Svc\UtilBundle\SvcUtilBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Test kernel
 */
class SvcProfileTestingKernel extends Kernel
{

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


  public function registerBundles(): array
  {
    return [
      new FrameworkBundle(),
      new DoctrineBundle(),
      new SvcProfileBundle(),
      new SvcUtilBundle(),
      new TwigBundle()
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
          'auto_generate_proxy_classes' => false,
          'auto_mapping' => false,
  //        'mappings' => [
  //          'App' => [
  //            'is_bundle' => false,
  //            'type' => 'annotation',
  //            'dir' => 'tests/Fixtures/Entity/',
  //            'prefix' => 'SymfonyCasts\Bundle\ResetPassword\Tests\Fixtures\Entity',
  //            'alias' => 'App',
  //          ],
  //        ],
        ],
      ]);

      /*
      $container->register(ResetPasswordTestFixtureRequestRepository::class)
        ->setAutoconfigured(true)
        ->setAutowired(true);

      $container->loadFromExtension('symfonycasts_reset_password', [
        'request_password_repository' => ResetPasswordTestFixtureRequestRepository::class,
      ]);

      $container->register('kernel', static::class)
        ->setPublic(true);
      */

//      $kernelDefinition = $container->getDefinition('kernel');
//      $kernelDefinition->addTag('routing.route_loader');
    });
  }

  /**
   * @internal
   */
  public function loadRoutes(LoaderInterface $loader): RouteCollection
  {
    $routes = new RouteCollection();

    foreach ($this->routes as $name => $path) {
      $routes->add($name, new Route($path));
    }

    return $routes;
  }
}
