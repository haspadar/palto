<?php

namespace Palto;

use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    public static function filterPriceCurrency(string $price): array
    {
        $parts = explode(' ', $price);
        $currency = $parts[count($parts) - 1];
        unset($parts[count($parts) - 1]);

        return [self::filterPrice(implode(' ', $parts)), $currency];
    }

    public static function filterPrice(string $price): float
    {
        $filtered = floatval(strtr($price, [',' => '', ' ' => '']));

        return min($filtered, 99999999.99);
    }

    public static function getDonorUrl(): string
    {
        if (isset($_SERVER['argv'][1])) {
            $parsed = parse_url($_SERVER['argv'][1]);

            return $parsed['scheme'] . '://' . $parsed['host'];
        }

        return '';
    }

    public static function hasNextPageLinkTag($categoryDocument): bool
    {
        return $categoryDocument->filter('link[rel=next]')->count() > 0;
    }

    public static function getNextPageUrl($categoryDocument): string
    {
        return $categoryDocument->filter('link[rel=next]')->attr('href');
    }

    public static function checkDonorUrl()
    {
        if (!isset($_SERVER['argv'][1])) {
            exit('Укажите первым параметром URL страницы, например: php parse_ads.php https://losangeles.craigslist.org' . PHP_EOL);
        }
    }

    public static function getJsVariable($adDocument, string $variableName, $endCharacter = ';'): array
    {
        $foundVariable = [];
        $adDocument->filter('script[type="text/javascript"]')->each(
            function (Crawler $resultRow, $i) use ($variableName, &$foundVariable, $endCharacter) {
//                $fullCode = trim(stripslashes(html_entity_decode($resultRow->html())));
                $fullCode = $resultRow->html();
                if (($position = mb_strpos($fullCode, $variableName)) !== false) {
                    $variableEndPosition = mb_strpos($fullCode, "\";", $position);
                    $variableWithNameAndQuote = mb_substr($fullCode, $position, $variableEndPosition - $position);
                    $firstQuotePosition = mb_strpos($variableWithNameAndQuote, '"');
                    $variableAfterFirstQuote = substr($variableWithNameAndQuote, $firstQuotePosition + 1);
                    $cleanedUp = trim(stripslashes(html_entity_decode($variableAfterFirstQuote)));
                    file_put_contents('test.json', $cleanedUp);
                    $foundVariable = \json_decode($cleanedUp, true);
                }
            }
        );

        return $foundVariable ?? [];
    }
}
