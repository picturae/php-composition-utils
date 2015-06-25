<?php

/**
 * Small trait for configurable extraction of properties in array format from an object.
 * @package    Picturae\Utils
 * @author     Boyan Bonev <b.bonev@bluebuffstudio.com>
 * @copyright  2015 Picturae LLC
 **/

namespace Picturae\Utils;

trait WithArrayTransform
{
    /**
     * Caches the list of fields available for a certain object.
     * @var array
     */
    private $fieldsList = null;


    /**
     * Fetch the array representation of the protected properties of an object.
     * @return array
     **/
    public function toArray()
    {
        $classData = [];
        foreach ($this->getReflector()->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            if (!$reflectionProperty->isStatic()) {
                $reflectionProperty->setAccessible(true);
                $classData[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
            }
        }
        return $classData;
    }

    /**
     * Set protected propertie according to provided source.
     * @param array source data.
     * @return $this
     **/
    public function fromArray(array $source)
    {
        foreach ($this->getReflector()->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            if (!$reflectionProperty->isStatic() && isset($source[$reflectionProperty->getName()])) {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($this, $source[$reflectionProperty->getName()]);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        if (null === $this->fieldsList) {
            $this->fieldsList = [];

            foreach ($this->getReflector()->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
                if (!$reflectionProperty->isStatic()) {
                    $this->fieldsList[] = $reflectionProperty->getName();
                }
            }
        }

        return $this->fieldsList;
    }
}
