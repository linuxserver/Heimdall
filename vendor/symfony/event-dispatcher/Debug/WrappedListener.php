<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Debug;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\VarDumper\Caster\ClassStub;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class WrappedListener
{
    private $listener;
    private $name;
    private $called;
    private $stoppedPropagation;
    private $stopwatch;
    private $dispatcher;
    private $pretty;
    private $stub;
    private static $hasClassStub;

    public function __construct($listener, $name, Stopwatch $stopwatch, EventDispatcherInterface $dispatcher = null)
    {
        $this->listener = $listener;
        $this->stopwatch = $stopwatch;
        $this->dispatcher = $dispatcher;
        $this->called = false;
        $this->stoppedPropagation = false;

        if (\is_array($listener)) {
            $this->name = \is_object($listener[0]) ? \get_class($listener[0]) : $listener[0];
            $this->pretty = $this->name.'::'.$listener[1];
        } elseif ($listener instanceof \Closure) {
            $r = new \ReflectionFunction($listener);
            if (false !== strpos($r->name, '{closure}')) {
                $this->pretty = $this->name = 'closure';
            } elseif ($class = $r->getClosureScopeClass()) {
                $this->name = $class->name;
                $this->pretty = $this->name.'::'.$r->name;
            } else {
                $this->pretty = $this->name = $r->name;
            }
        } elseif (\is_string($listener)) {
            $this->pretty = $this->name = $listener;
        } else {
            $this->name = \get_class($listener);
            $this->pretty = $this->name.'::__invoke';
        }

        if (null !== $name) {
            $this->name = $name;
        }

        if (null === self::$hasClassStub) {
            self::$hasClassStub = class_exists(ClassStub::class);
        }
    }

    public function getWrappedListener()
    {
        return $this->listener;
    }

    public function wasCalled()
    {
        return $this->called;
    }

    public function stoppedPropagation()
    {
        return $this->stoppedPropagation;
    }

    public function getPretty()
    {
        return $this->pretty;
    }

    public function getInfo($eventName)
    {
        if (null === $this->stub) {
            $this->stub = self::$hasClassStub ? new ClassStub($this->pretty.'()', $this->listener) : $this->pretty.'()';
        }

        return array(
            'event' => $eventName,
            'priority' => null !== $this->dispatcher ? $this->dispatcher->getListenerPriority($eventName, $this->listener) : null,
            'pretty' => $this->pretty,
            'stub' => $this->stub,
        );
    }

    public function __invoke(Event $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $this->called = true;

        $e = $this->stopwatch->start($this->name, 'event_listener');

        \call_user_func($this->listener, $event, $eventName, $this->dispatcher ?: $dispatcher);

        if ($e->isStarted()) {
            $e->stop();
        }

        if ($event->isPropagationStopped()) {
            $this->stoppedPropagation = true;
        }
    }
}
