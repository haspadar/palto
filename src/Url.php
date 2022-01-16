<?php

namespace Palto;

class Url
{
    private const KARMAN = 'karman';

    /**
     * @var mixed|string
     */
    private string $url;
    private string $path;

    public function __construct(string $url = '')
    {
        $this->url = $url ?: ($_SERVER['REQUEST_URI'] ?? '/');
        $this->path = $this->initPath();
    }

    public function __toString(): string
    {
        return $this->url;
    }

    public function getDomain(): string
    {
        $parsed = parse_url($this->url);
        if (isset($parsed['scheme']) && isset($parsed['host'])) {
            return $parsed['scheme'] . '://' . $parsed['host'];
        }

        return '';
    }

    public function getFull(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRegionUrl(): string
    {
        $parts = self::getUrlParts($this->path);

        return $parts[0] ?? '';
    }

    public function getCategoriesUrls(): array
    {
        $parts = $this->getUrlParts($this->path);
        array_shift($parts);
        if (self::isAdPage($this->path)) {
            array_pop($parts);
        }

        return array_values(array_filter($parts));
    }

    public function getQueryParameters(): array
    {
        $path = parse_url($this->url);
        parse_str($path['query'] ?? '', $parameters);
        foreach ($parameters as &$value) {
            $value = Filter::get($value);
        }

        return $parameters;
    }

    public function getAdId(): int
    {
        $last = $this->getLastPart($this->path);

        return intval(mb_substr($last, 2));
    }

    public function isKarmanPage(): bool
    {
        return $this->getParts() == self::KARMAN;
    }

    public function isRegionPage(): bool
    {
        return count($this->getParts()) == 1;
    }

    public function generateParentUrl(): self
    {
        $parts = $this->getParts();
        array_pop($parts);

        return new self(
            '/'
            . implode('/', $parts)
            . ($this->getQueryParameters()
                ? '?' . http_build_query($this->getQueryParameters())
                : ''
            )
        );
    }

    public function isAdPage(): bool
    {
        $last = $this->getLastPart($this->path);

        return mb_substr($last, 0, 2) == 'ad' && is_numeric($this->getAdId());
    }

    public function getPageNumber(): int
    {
        if ($this->hasUrlPageNumber()) {
            $withoutQueryUrl = parse_url($this->url)['path'];

            return $this->getLastPart($withoutQueryUrl);
        }

        return 1;
    }

    public function withoutPageNumber(string $path): string
    {
        if ($this->hasUrlPageNumber()) {
            $parts = $this->getUrlParts($path);
            array_pop($parts);

            return implode('/', array_filter($parts));
        }

        return $path;
    }

    private function getParts(): array
    {
        return array_values(array_filter(explode('/', $this->path)));
    }

    private function hasUrlPageNumber(): bool
    {
        $withoutQueryUrl = parse_url($this->url)['path'];
        $parts = self::getUrlParts($withoutQueryUrl);

        return isset($parts[count($parts) - 1]) && is_numeric($parts[count($parts) - 1]);
    }

    private function getLastPart(string $path): string
    {
        $parts = $this->getUrlParts($path);

        return $parts[count($parts) - 1] ?? '';
    }

    private function getUrlParts(string $url): array
    {
        return array_values(array_filter(explode('/', $url)));
    }

    private function initPath(): string
    {
        $path = parse_url($this->url)['path'];
        if (mb_substr($path, -1) == '/' && $path != '/') {
            $path = mb_substr($path, 0, -1);
        }

        if ($this->hasUrlPageNumber()) {
            $path = $this->withoutPageNumber($path);
        }

        return $path;
    }
}