<?php

namespace Svc\ProfileBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SvcProfileBundle extends Bundle {

  public function getPath(): string
  {
      return \dirname(__DIR__);
  }
}