<?php
/**
 * Created by: Yaroslav Pohil
 * Date and time: 22/12/2020 12:26
 */

namespace CompleteSolar\ApiClientsTests;

use Illuminate\Contracts\Events\Dispatcher;

class TestEventDispatcher implements Dispatcher
{
    protected $listeners = [];

    public function listen($events, $listener)
    {
        $this->listeners[$events] = $listener;
    }

    public function hasListeners($eventName)
    {
        return array_key_exists($eventName, $this->listeners);
    }

    public function subscribe($subscriber)
    {
        // TODO: Implement subscribe() method.
    }

    public function until($event, $payload = [])
    {
        if ($this->hasListeners($event)) {
            return call_user_func($this->listeners[$event], $payload);
        }

        return null;
    }

    public function dispatch($event, $payload = [], $halt = false)
    {
        // TODO: Implement dispatch() method.
    }

    public function push($event, $payload = [])
    {
        // TODO: Implement push() method.
    }

    public function flush($event)
    {
        // TODO: Implement flush() method.
    }

    public function forget($event)
    {
        // TODO: Implement forget() method.
    }

    public function forgetPushed()
    {
        // TODO: Implement forgetPushed() method.
    }
}