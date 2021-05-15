<?php

namespace App\Service\Api;

interface ApiInterface
{
  public function configure(array $options);

  public function connect(array $options);
}
