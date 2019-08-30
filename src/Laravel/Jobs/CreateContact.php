<?php

namespace Balfour\Omnisend\Laravel\Jobs;

use Balfour\Omnisend\ContactStatus;
use Balfour\Omnisend\ContactInterface;
use Balfour\Omnisend\Omnisend;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ContactInterface
     */
    protected $contact;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var bool
     */
    protected $sendWelcomeEmail;

    /**
     * @var string|null
     */
    protected $optInIp;

    /**
     * @var CarbonInterface|null
     */
    protected $optInDate;

    /**
     * Create a new job instance.
     *
     * @param ContactInterface $contact
     * @param string|null $status
     * @param bool|null $sendWelcomeEmail
     * @param string|null $optInIp
     * @param CarbonInterface|null $optInDate
     */
    public function __construct(
        ContactInterface $contact,
        ?string $status = null,
        ?bool $sendWelcomeEmail = null,
        ?string $optInIp = null,
        ?CarbonInterface $optInDate = null
    ) {
        if ($status === null) {
            $status = config('omnisend.default_contact_status');
        }

        if ($sendWelcomeEmail === null) {
            $sendWelcomeEmail = config('omnisend.send_welcome_email');
        }

        $this->contact = $contact;
        $this->status = $status;
        $this->sendWelcomeEmail = $sendWelcomeEmail;
        $this->optInIp = $optInIp;
        $this->optInDate = $optInDate;
    }

    /**
     * Execute the job.
     *
     * @param Omnisend $omnisend
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Omnisend $omnisend): void
    {
        $omnisend->createContact(
            $this->contact,
            $this->status,
            null,
            $this->sendWelcomeEmail,
            $this->optInIp,
            $this->optInDate
        );
    }

    /**
     * @param ContactInterface $contact
     * @param string $status
     * @param bool $sendWelcomeEmail
     * @param string|null $optInIp
     * @param CarbonInterface|null $optInDate
     */
    public static function enqueue(
        ContactInterface $contact,
        ?string $status = null,
        ?bool $sendWelcomeEmail = null,
        ?string $optInIp = null,
        ?CarbonInterface $optInDate = null
    ): void {
        static::dispatch($contact, $status, $sendWelcomeEmail, $optInIp, $optInDate)->onQueue(config('omnisend.queue'));
    }
}
