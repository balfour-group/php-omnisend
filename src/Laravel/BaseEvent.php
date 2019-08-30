<?php

namespace Balfour\Omnisend\Laravel;

use Balfour\Omnisend\EventInterface;
use Balfour\Omnisend\Laravel\Jobs\TriggerEvent;
use Balfour\Omnisend\Omnisend;

abstract class BaseEvent implements EventInterface
{
    /**
     * @param string $email
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fire(string $email): void
    {
        if (config('omnisend.enabled')) {
            /** @var Omnisend $omnisend */
            $omnisend = app(Omnisend::class);
            $omnisend->triggerEvent($this, $email);
        }
    }

    /**
     * @param string $email
     * @param string|null $queue
     */
    public function enqueue(string $email, ?string $queue = null): void
    {
        if (config('omnisend.enabled')) {
            $queue = $queue ?? config('omnisend.queue');

            TriggerEvent::dispatch($this, $email)->onQueue($queue);
        }
    }
}
