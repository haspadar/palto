<?php

namespace Palto;

class Translate
{
    private array $translate;

    public function __construct(array $translate)
    {
        $this->translate = $translate;
    }

    public function getName()
    {
        return $this->translate['name'];
    }

    public function getValue()
    {
        return $this->translate['value'];
    }

    public function getId()
    {
        return $this->translate['id'];
    }
}