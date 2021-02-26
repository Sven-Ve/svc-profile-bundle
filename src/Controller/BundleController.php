<?php

namespace Svc\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BundleController extends AbstractController
{

  public function index(): Response
  {
 //   return $this->render(__DIR__ . '/../../templates/profile/index.html.twig');
    return $this->render('@SvcProfile/profile/index.html.twig');
    return $this->render('@SvcProfileBundle/profile/index.html.twig');
    return new Response("Hallo from Bundle 1");
  }

}
