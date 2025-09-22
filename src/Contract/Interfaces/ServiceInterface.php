<?php

namespace App\Contract\Interfaces;

interface ServiceInterface
{
    public function call(array $params = []): ServiceResponseInterface;
}
