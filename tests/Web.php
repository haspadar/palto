<?php

namespace Test;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Logger;
use Palto\Model\Categories;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class Web extends TestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
    }

    public function checkPhpErrors(Response $response)
    {
        $patterns = [
            '<b>Notice</b>: ',
            '<b>Warning</b>: ',
            '<b>Fatal error</b>:',
            '<b>Parse error</b>:'
        ];
        foreach ($patterns as $pattern) {
            $start = mb_strpos($response->getHtml(), $pattern);
            if ($start !== false) {
                $finish = mb_strpos($response->getHtml(), '<br>', $start);
                $notification = mb_substr($response->getHtml(), $start, $finish - $start);

                throw new Exception(
                    'Found Php Notification Text: ' . $notification . ' on page ' . $response->getUrl()
                );
            }
        }
    }

    protected function checkLinks(Response $response)
    {
        $categoryDocument = new Crawler($response->getHtml());
        $links = $categoryDocument->filter('.table_main a');
        $this->assertTrue($links->count() > 0, 'Page hasn\'t links: ' . $response->getUrl());
        $this->assertTrue($response->getHttpCode() == 200, 'Http response code: ' . $response->getHttpCode());
    }

    protected function download(string $url)
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL,$this->getDomainUrl() . $url . '?debug=1');
        \curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
        $result = \curl_exec($ch);
        $info = curl_getinfo($ch);
        \curl_close ($ch);
        if ($info['total_time'] > 3) {
            Logger::warning('Long time request: ' . $info['total_time'] . ' seconds for ' . $this->getDomainUrl() . $url);
        }

        return new Response($result, $info['http_code'], $this->getDomainUrl() . $url, $info['redirect_url']);
    }

    protected function getDomainUrl(): string
    {
        return Config::get('DOMAIN_URL');
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