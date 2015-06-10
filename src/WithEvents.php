<?php

/**
 * Utility trait providing a specific class with the ability to easily
 * manage an observer-like events pattern.
 * @package    Picturae\Utils
 * @author     Boyan Bonev <b.bonev@bluebuffstudio.com>
 * @copyright  2015 Picturae LLC
 **/

namespace Picturae\Utils;

use RuntimeException;

trait WithEvents
{
    /**
     * Keeps the object event list.
     * @var array
     **/
    private $eventList = [];

    /**
     * Add a callback on a specific backend event.
     * @param string $eventType - Use constants(add your own if needed)
     * @param callable $callback - Execution callback.
     * @return string|null
     **/
    public function subscribe($eventType, $callback)
    {
        $callback = $this->makeEventCallback($callback);
        if (!isset($this->eventList[$eventType]) || !is_array($this->eventList[$eventType])) {
            $this->eventList[$eventType] = [];
        }
        array_push($this->eventList[$eventType], $callback);
        return $callback['id'];
    }

    /**
     * Remove callback by identifier.
     * @param string
     * @param string
     **/
    public function unsubscribe($eventType, $callbackId)
    {
        $success = false;
        if (!isset($this->eventList[$eventType]) || !is_array($this->eventList[$eventType])) {
            $this->eventList[$eventType] = array_filter(
                $this->eventList[$eventType],
                function ($cb) use ($callbackId) {
                    return $cb['id'] !== $callbackId;
                }
            );
            $success = true;
        }
        return $success;
    }

    /**
     * Fire an event
     * @param string $event - Event type constant.
     * @param array $params - Additional parameters.
     **/
    public function trigger($eventType, $params)
    {
        if (isset($this->eventList[$eventType]) && is_array($this->eventList[$eventType])) {
            foreach ($this->eventList[$eventType] as $callbackDescriptor) {
                call_user_func_array($callbackDescriptor['cb'], $params);
            }
        }
    }

    /**
     * @param string
     * @return $this
     **/
    public function clear($eventType)
    {
        if (isset($this->eventList[$eventType])) {
            unset($this->eventList[$eventType]);
        }
        return $this;
    }

    /**
     * @param callable $callback
     * @return array
     **/
    private function makeEventCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new RuntimeException('Please pass a callable parameter for event trigger registration.');
        }
        return [
            'id' => uniqid(),
            'cb' => $callback
        ];
    }
}