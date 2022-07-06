<?php

namespace Palto;

class Translate
{
    private array $translate;

    public function __construct(array $translate)
    {
        $this->translate = $translate;
    }

    public function get()
    {
        return $this->translate['value'];
    }
}