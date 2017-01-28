<?php

namespace Profounder;

use Symfony\Component\Stopwatch\Stopwatch;

trait Benchmarkable {
    /**
     * Stopwatch instance.
     *
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * Latest event name.
     *
     * @var string
     */
    private $lastEvent;

    /**
     * Benchmarks the callable.
     *
     * @param  \Closure $closure
     * @param  null $eventName
     *
     * @return mixed
     */
    public function benchmark($closure, $eventName = null)
    {
        $this->lastEvent = $eventName = $eventName ?: $this->generateEventName();

        $returnValue = $closure($this->stopwatch()->start($eventName), $eventName);

        $this->stopwatch->stop($eventName);

        return $returnValue;
    }

    /**
     * Return an event's diration in milliseconds.
     *
     * @param  null $eventName
     *
     * @return int
     */
    public function elapsed($eventName = null)
    {
        $eventName = $eventName ?: $this->lastEvent;

        return $this->stopwatch->getEvent($eventName)->getDuration();
    }

    /**
     * Stopwatch singleton factory.
     *
     * @return Stopwatch
     */
    private function stopwatch()
    {
        return $this->stopwatch ?: $this->stopwatch = new Stopwatch;
    }

    /**
     * Generates a random stopwatch event name.
     *
     * @return string
     */
    private function generateEventName()
    {
        return 'benchmark.event.' . uniqid();
    }
}
