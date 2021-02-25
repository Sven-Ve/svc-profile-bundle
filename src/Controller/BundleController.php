<?php

namespace Svc\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BundleController extends AbstractController
{

  public function index(): Response
  {
    return new Response("Hallo from Bundle 1");
  }

}
