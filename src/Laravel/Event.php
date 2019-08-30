<?php

namespace Balfour\Omnisend\Laravel;

class Event extends BaseEvent
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @param string $id
     * @param array $fields
     */
    public function __construct(string $id, array $fields = [])
    {
        $this->id = $id;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
