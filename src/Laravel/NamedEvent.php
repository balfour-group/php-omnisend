<?php

namespace Balfour\Omnisend\Laravel;

use Balfour\Omnisend\Omnisend;
use Carbon\Carbon;
use Exception;

class NamedEvent extends BaseEvent
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed[]
     */
    protected $fields;

    /**
     * @param string $name
     * @param mixed[] $fields
     */
    public function __construct(string $name, array $fields = [])
    {
        $this->name = $name;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getID(): string
    {
        $key = md5(sprintf('omnisend.event[name="%s"]', $this->name));

        $event = cache()->get($key);

        if ($event) {
            return $event['eventID'];
        } else {
            /** @var Omnisend $omnisend */
            $omnisend = app(Omnisend::class);
            $event = $omnisend->getEventByName($this->name);

            // we only cache the event object if the event exists
            if ($event) {
                cache()->put($key, $event, Carbon::now()->addDay());

                return $event['eventID'];
            }

            throw new Exception(sprintf('The event name "%s" could not be resolved to an id.', $this->name));
        }
    }
}
