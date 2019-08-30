<?php

namespace Balfour\Omnisend\Laravel\Jobs;

use Balfour\Omnisend\EventInterface;
use Balfour\Omnisend\Omnisend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TriggerEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var EventInterface
     */
    protected $event;

    /**
     * @var string
     */
    protected $email;

    /**
     * Create a new job instance.
     *
     * @param EventInterface $event
     * @param string $email
     */
    public function __construct(EventInterface $event, string $email)
    {
        $this->event = $event;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @param Omnisend $omnisend
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Omnisend $omnisend): void
    {
        $omnisend->triggerEvent($this->event, $this->email);
    }

    /**
     * @param EventInterface $event
     * @param string $email
     */
    public static function enqueue(EventInterface $event, string $email): void
    {
        static::dispatch($event, $email)->onQueue(config('omnisend.queue'));
    }
}
