<?php

namespace Test;

class Response
{
    private string $html;
    private int $httpCode;
    private string $redirectUrl;

    public function __construct(string $html, int $httpCode, string $redirectUrl)
    {
        $this->html = $html;
        $this->httpCode = $httpCode;
        $this->redirectUrl = $redirectUrl;
    }

    public function getHtml(): string
    {
        return $this->html;
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
}