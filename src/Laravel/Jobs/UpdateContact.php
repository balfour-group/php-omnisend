<?php

namespace Balfour\Omnisend\Laravel\Jobs;

use Balfour\Omnisend\ContactInterface;
use Balfour\Omnisend\Omnisend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ContactInterface
     */
    protected $contact;

    /**
     * Create a new job instance.
     *
     * @param ContactInterface $contact
     */
    public function __construct(ContactInterface $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     *
     * @param Omnisend $omnisend
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Omnisend $omnisend): void
    {
        $omnisend->updateContactByEmail($this->contact->getEmail(), $this->contact);
    }

    /**
     * @param ContactInterface $contact
     */
    public static function enqueue(ContactInterface $contact): void
    {
        static::dispatch($contact)->onQueue(config('omnisend.queue'));
    }
}
