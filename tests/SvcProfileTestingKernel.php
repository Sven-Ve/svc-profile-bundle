<?php

namespace Svc\ProfileBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Svc\ProfileBundle\SvcProfileBundle;
use Svc\UtilBundle\SvcUtilBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Test kernel
 */
class SvcProfileTestingKernel extends Kernel
{
    public function registerBundles() :array
    {
      return [
        new SvcProfileBundle(),
        new FrameworkBundle(),
        new DoctrineBundle(),
        new SvcUtilBundle()
      ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

}