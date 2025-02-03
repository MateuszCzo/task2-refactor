<?php

namespace Core\Utils;

use InvalidArgumentException;
use ReflectionClass;

class ObjectMapper
{
    public function map(string $destinationClassName, object $source)
    {
        $sourceData = $this->extractData($source);
        return $this->mapFromArray($destinationClassName, $sourceData);
    }

    public function mapFromArray(string $destinationClassName, array $data)
    {
        $destinationClass = new ReflectionClass($destinationClassName);

        if ($constructor = $destinationClass->getConstructor()) {
            return $this->instantiateWithConstructor($destinationClass, $constructor, $data);
        }

        return $this->instantiateWithoutConstructor($destinationClass, $data);
    }

    protected function extractData(object $model): array
    {
        $reflection = new ReflectionClass($model);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($model);
        }

        return $data;
    }

    protected function instantiateWithConstructor(ReflectionClass $destinationClass, $constructor, array $data)
    {
        $constructorParams = $constructor->getParameters();
        $constructorArguments = [];

        foreach ($constructorParams as $param) {
            $paramName = $param->getName();
            if (array_key_exists($paramName, $data)) {
                $constructorArguments[] = $data[$paramName];
            } elseif ($param->isOptional()) {
                $constructorArguments[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException('Missing required parameter: ' . $paramName);
            }
        }

        return $destinationClass->newInstanceArgs($constructorArguments);
    }

    protected function instantiateWithoutConstructor(ReflectionClass $destinationClass, array $data)
    {
        $destination = $destinationClass->newInstance();

        foreach ($destinationClass->getProperties() as $property) {
            $propertyName = $property->getName();
            if (array_key_exists($propertyName, $data)) {
                $property->setAccessible(true);
                $property->setValue($destination, $data[$propertyName]);
            }
        }

        return $destination;
    }
}