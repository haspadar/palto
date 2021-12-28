<?php

namespace Test;

class Response
{
    private string $html;
    private int $httpCode;
    private string $redirectUrl;
    private string $url;

    public function __construct(string $html, int $httpCode, string $url, string $redirectUrl)
    {
        $this->html = $html;
        $this->httpCode = $httpCode;
        $this->url = $url;
        $this->redirectUrl = $redirectUrl;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function isJson(): bool
    {
        json_decode($this->getHtml());

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}