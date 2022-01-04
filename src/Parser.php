<?php

namespace Palto;

use Cocur\Slugify\Slugify;
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

    public static function filterPrice(string $price): string 
    {
        $filtered = strtr($price, [',' => '', ' ' => '']);

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

    public static function upperCaseEveryWord(string $text): string
    {
        $words = explode(' ', $text);
        foreach ($words as &$word) {
            $word = ucfirst($word);
        }

        return implode(' ', $words);
    }

    public static function safeTransaction(Callable $function)
    {
        try {
            \Palto\Model\Ads::getDb()->startTransaction();
            $return = $function();
            \Palto\Model\Ads::getDb()->commit();

            return $return;
        } catch (\Exception $e) {
            \Palto\Model\Ads::getDb()->rollback();
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
            if (!Cli::isCron() && Cli::isCli()) {
                exit;
            }
        }
    }

    public static function hasNextPageLinkTag(Crawler $categoryDocument): bool
    {
        return $categoryDocument->filter('link[rel=next]')->count() > 0;
    }

    public static function getNextPageNumber(Crawler $categoryDocument): int
    {
        $url = self::getNextPageUrl($categoryDocument)->getFull();
        if (self::getLastSymbol($url) == '/') {
            $url = self::removeLastSymbol($url);
        }

        $pageNumberSymbols = [];
        while ($url && is_numeric(self::getLastSymbol($url))) {
            $pageNumberSymbols[] = self::getLastSymbol($url);
            $url = self::removeLastSymbol($url);
        }

        return implode('', array_reverse($pageNumberSymbols));
    }

    public static function getNextPageUrl(Crawler $categoryDocument): ?Url
    {
        $url = $categoryDocument->filter('link[rel=next]')->attr('href');

        return $url ? new Url($url) : null;
    }

    public static function checkDonorUrl(): string
    {
        if (!isset($_SERVER['argv'][1])) {
            exit('Укажите первым параметром URL страницы, например: php parse_ads.php https://losangeles.craigslist.org' . PHP_EOL);
        }

        return $_SERVER['argv'][1];
    }

    public static function findSelectorWithContent(Crawler $adDocument, array $selectors): array
    {
        foreach ($selectors as $selector) {
            if ($adDocument->filter($selector)->count() > 0) {
                return [$selector, $adDocument->filter($selector)->html()];
            }
        }

        return ['', ''];
    }

    public static function findContent(Crawler $adDocument, array $selectors): string
    {
        list($_, $content) = self::findSelectorWithContent($adDocument, $selectors);

        return $content;
    }

    public static function findSelector(Crawler $adDocument, array $selectors): string
    {
        list($selector, $_) = self::findSelectorWithContent($adDocument, $selectors);

        return $selector;
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
//                    file_put_contents('test.json', $cleanedUp);
                    $foundVariable = \json_decode($cleanedUp, true);
                }
            }
        );

        return $foundVariable ?? [];
    }

    private static function removeLastSymbol(string $string): string
    {
        return substr($string, 0, -1);
    }

    private static function getLastSymbol(string $string): string
    {
        return substr($string, -1);
    }
}
