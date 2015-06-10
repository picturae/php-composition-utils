<?php

/**
 * Utility trait providing a specific class with reflection
 * properties, constructor & setters & getters.
 * Again, by convention, operates only on protected properties.
 * @package    Picturae\Utils
 * @author     Boyan Bonev <b.bonev@bluebuffstudio.com>
 * @copyright  2015 Picturae LLC
 **/

namespace Picturae\Utils;

use \ReflectionClass;
use \ReflectionProperty;
use \RuntimeException;

trait WithFullReflection
{
    /**
     * @var \ReflectionClass
     **/
    private $reflector = null;

    /**
     * Abstract class properties can not be set,
     * all properties declared protected in sub classes will be set.
     * This has a significant performance overhead if used often.
     * @param array
     **/
    public function __construct(array $properties = [])
    {
        $reflector = $this->getReflector();
        foreach ($reflector->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            if (!$property->isStatic() && !empty($properties[$property->getName()])) {
                $this->{'set' . ucfirst($property->getName())}(
                    $properties[$property->getName()]
                );
            }
        }
    }

    /**
     * Fetch the reflector.
     * @return \ReflectionClass
     **/
    protected function getReflector()
    {
        if (is_null($this->reflector)) {
            $this->setReflector();
        }
        return $this->reflector;
    }

    /**
     * Initialize the reflector property, accept subject as parameter
     * or default to $this.
     * @param String|object
     * @return $this
     **/
    protected function setReflector($reflectionSubject = null)
    {
        if (is_null($reflectionSubject)) {
            $reflectionSubject = $this;
        }
        $this->reflector = new \ReflectionClass($reflectionSubject);
        return $this;
    }

    /**
     * Use reflection for getters and setters if method is missing
     * implemented.
     * Result of this: calling "getName" will fetch the $name property
     * without explicit existance of the function, same for setters.
     * @param string method name
     * @param array arguments for the method call.
     * @return mixed
     **/
    public function __call($methodName, $methodArguments)
    {
        $operationType = substr($methodName, 0, 3);
        $reflector = $this->getReflector();

        if (!$reflector->hasMethod($operationType)) {
            throw new RuntimeException('Trying to call undefined method in reflection context: ' . $methodName);
        }

        //Prepend's operand as first argument
        array_unshift(
            $methodArguments,
            lcfirst(substr($methodName, 3))
        );

        $reflectionMethod = $reflector->getMethod($operationType);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs(
            $this,
            $methodArguments
        );
    }

    /**
     * Reflection property setter.
     * @param string property subject.
     * @param mixed property value.
     * @return $this
     **/
    private function set($operand, $value)
    {
        $reflector = $this->getReflector();

        if (!$reflector->hasProperty($operand)) {
            throw new RuntimeException('Trying to access undefined property in reflection context: ' . $operand);
        }

        $reflectionProperty = $reflector->getProperty($operand);
        if ($reflectionProperty->isPrivate()) {
            throw new RuntimeException(
                'Private properties are not accessible by default in the reflection context.' . $operand
            );
        }

        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue(
            $this,
            $value
        );

        return $this;
    }

    /**
     * Reflected p[roperties defauilt getter.
     * @param string property subject
     **/
    private function get($operand)
    {
        $reflector = $this->getReflector();

        if (!$reflector->hasProperty($operand)) {
            throw new RuntimeException('Trying to access undefined property from reflection context: ' . $operand);
        }

        $reflectionProperty = $reflector->getProperty($operand);
        if ($reflectionProperty->isPrivate()) {
            throw new RuntimeException(
                'Private properties are not allowed to be set from reflection context.' . $operand
            );
        }

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($this);
    }
}