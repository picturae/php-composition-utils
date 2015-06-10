<?php

/**
 * Utility trait attaching a \Reflector property to classes.
 * @package    Picturae\Utils
 * @author     Boyan Bonev <b.bonev@bluebuffstudio.com>
 * @copyright  2015 Picturae LLC
 **/

namespace Picturae\Utils;

use \ReflectionClass;

trait WithReflector
{
    /**
     * @var \ReflectionClass
     **/
    private $reflector = null;

    /**
     * @desc Initialize reflector and fetch the \Reflection class.
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
     * @desc Create a reflection object for the current class or for a
     * specific instance.
     * @param String|Object - default current class.
     * @return $this
     **/
    protected function setReflector($reflectionSubject = null)
    {
        if (is_null($reflectionSubject)) {
            $reflectionSubject = $this;
        }
        $this->reflector = new ReflectionClass($reflectionSubject);
        return $this;
    }
}
