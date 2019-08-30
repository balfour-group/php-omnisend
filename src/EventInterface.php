<?php

namespace Balfour\Omnisend;

interface EventInterface
{
    /**
     * @return string
     */
    public function getID(): string;

    /**
     * @return array
     */
    public function getFields(): array;
}
