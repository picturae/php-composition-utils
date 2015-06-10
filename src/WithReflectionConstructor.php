<?php

/**
 * Utility trait providing a specific class with constructor for all
 * properties of type protected from an array.
 * @package    Picturae\Utils
 * @author     Boyan Bonev <b.bonev@bluebuffstudio.com>
 * @copyright  2015 Picturae LLC
 **/

namespace Picturae\Utils;

use \ReflectionClass;
use \ReflectionProperty;

trait WithReflectionConstructor
{
    /**
     * Abstract class properties can not be set,
     * all properties declared protected in sub classes will be set.
     * @param array
     **/
    public function __construct(array $properties = [])
    {
        if (!empty($properties)) {
            $reflector = new ReflectionClass($this);
            foreach ($reflector->getProperties(ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
                if (!$reflectionProperty->isStatic() && isset($properties[$reflectionProperty->getName()])) {
                    $reflectionProperty->setAccessible(true);
                    $reflectionProperty->setValue(
                        $this,
                        $properties[$reflectionProperty->getName()]
                    );
                }
            }
        }
    }
}