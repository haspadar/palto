<?php

namespace Test;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Logger;
use Palto\Model\Categories;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;

abstract class Web extends TestCase
{
    private static \MeekroDB $db;

    const DATABASE = 'palto_test';

    public function __construct(string $name)
    {
        parent::__construct($name);
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
    }

    protected function download(string $url)
    {
        return file_get_contents($this->getDomainUrl() . $url);
    }

    protected function getDomainUrl(): string
    {
        return Config::get('DOMAIN_URL');
    }

    protected function checkPhpErrors(string $content): bool
    {
        $patterns = [
            '<b>Notice</b>: ',
            '<b>Warning</b>: ',
            '<b>Fatal error</b>:',
            '<b>Parse error</b>:'
        ];
        foreach ($patterns as $pattern) {
            $start = mb_strpos($content, $pattern);
            if ($start !== false) {
                $finish = mb_strpos($content, '<br>', $start);
                $notification = mb_substr($content, $start, $finish - $start);

                throw new Exception('Found Php Notification Text: ' . $notification);
            }
        }

        return true;
    }

    protected function assertUrl(string $href, string $urlWithText, string $haystack)
    {
        $start = mb_strpos($haystack, 'href="' . $href . '"');
        $this->assertTrue($start !== false, 'Link "' . $href . '" not found');
        $closedFirstA = mb_strpos($haystack, '>', $start);
        $openedSecondA = mb_strpos($haystack, '</a>', $closedFirstA);
        $urlText = mb_substr($haystack, $closedFirstA + 1, $openedSecondA - $closedFirstA - 1);

        $this->assertStringContainsString(
            $urlWithText,
            $urlText,
            'Url text "' . $urlText . '" doesn\'t contains "' . $urlWithText .'"'
        );
    }
}