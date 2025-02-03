<?php

namespace Core\Container;

interface ContainerInterface
{
    public function getService(string $name);

    public function getServices();

    public function serviceExists(string $name);

    public static function getInstance(): self;
}
