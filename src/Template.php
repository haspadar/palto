<?php

namespace Palto;

class Template
{
    private array $template;

    public function __construct(array $template)
    {
        $this->template = $template;
    }

    public function getName(): string
    {
        return $this->template['name'];
    }

    public function getId(): int
    {
        return $this->template['id'];
    }

    public function getShortName(): string
    {
        return str_replace('.php', '', $this->getName());
    }
}