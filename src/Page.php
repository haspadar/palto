<?php

namespace Palto;

class Page
{
    private array $page;
    private ?Template $template;

    public function __construct(array $page)
    {
        $this->page = $page;
        $this->template = $page['template_id'] ? Templates::getById($page['template_id']) : nul;
    }

    public function getTemplateId(): int
    {
        return $this->page['template_id'] ?? 0;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function is404(): string
    {
        return $this->getName() == '404';
    }

    public function getFunction(): string
    {
        return $this->page['function'];
    }

    public function getName(): string
    {
        return $this->page['name'];
    }

    public function getTitle(): string
    {
        return $this->page['title'];
    }

    public function getId(): int
    {
        return $this->page['id'];
    }

    public function getDescription(): string
    {
        return $this->page['description'];
    }

    public function isEnabled(): bool
    {
        return $this->page['is_enabled'] == 1;
    }

    public function getComment(): string
    {
        return $this->page['comment'];
    }

    public function getUrl(): string
    {
        return $this->page['url'];
    }

    public function getH1(): string
    {
        Debug::dump($this->page['h1'], 'h1');
        return $this->page['h1'];
    }

    public function getH2(): string
    {
        return $this->page['h2'];
    }

    public function getPriority(): int
    {
        return $this->page['priority'];
    }
}