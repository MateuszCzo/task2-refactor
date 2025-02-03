<?php

namespace Core\Container;

use Core\Exception\CyclicDependencyException;
use InvalidArgumentException;
use OutOfBoundsException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Container implements ContainerInterface
{
    private static $instance = null;

    private array $serviceData = [];
    private array $serviceInstances = [];

    private array $resolving = [];

    public function __construct(string ...$servicesFiles)
    {
        self::$instance = $this;
        $this->serviceInstances['container'] = $this;

        $serviceData = [];

        foreach ($servicesFiles as $servicesFile) {
            $serviceData = array_merge($serviceData, json_decode(file_get_contents($servicesFile), true, 512, JSON_THROW_ON_ERROR));
        }

        $this->serviceData = $serviceData;

        foreach ($serviceData as $name => $data) {
            $this->loadService($name, $data);
        }
    }

    public function setService($name, $instance)
    {
        if (isset($this->serviceInstances[$name])) {
            throw new InvalidArgumentException('Service already exists: ' . $name);
        }

        $this->serviceInstances[$name] = $instance;
    }

    protected function loadService($name, $data)
    {
        if (isset($this->serviceInstances[$name])) {
            return;
        }
        if (isset($this->resolving[$name])) {
            throw new CyclicDependencyException('Cyclic dependency detected for service: ' . $name);
        }
        if (!isset($data['class'])) {
            throw new InvalidArgumentException('No class name detected for service: ' . $name);
        }

        $this->resolving[$name] = true;

        $className = $data['class'];
        $dependencies = $this->resolveDependencies($data['args'] ?? []);

        $serviceInstance = $this->createInstance($className, $dependencies);

        $this->serviceInstances[$name] = $serviceInstance;

        unset($this->resolving[$name]);
    }

    protected function resolveDependencies(array $arguments)
    {
        $dependencies = [];

        foreach ($arguments as $arg) {
            if (is_string($arg) && strpos($arg, '@') === 0) {
                $serviceName = substr($arg, 1);
                if (!isset($this->serviceInstances[$serviceName])) {
                    $this->loadService($serviceName, $this->serviceData[$serviceName]);
                }
                $dependencies[] = $this->serviceInstances[$serviceName];
            } else {
                $dependencies[] = $arg;
            }
        }

        return $dependencies;
    }

    protected function createInstance($className, $dependencies)
    {
        try {
            $reflectionClass = new ReflectionClass($className);
            $constructor = $reflectionClass->getConstructor();

            if (!$constructor) {
                return new $className();
            }

            return $reflectionClass->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new ReflectionException('Failed to create instance of ' . $className . ': ' . $e->getMessage());
        }
    }

    public function getService(string $name)
    {
        if (!isset($this->serviceInstances[$name])) {
            throw new OutOfBoundsException('Service not found: ' . $name);
        }

        return $this->serviceInstances[$name];
    }

    public function getServices()
    {
        return $this->serviceInstances;
    }

    public function serviceExists(string $name)
    {
        return isset($this->serviceInstances[$name]);
    }

    public static function getInstance(): self
    {
        if (self::$instance == null) {
            throw new RuntimeException('No container available.');
        }
        return self::$instance;
    }
}