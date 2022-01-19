<?php

namespace Palto;

use Symfony\Component\DomCrawler\Crawler;

class Yandex
{
    public static function translate(string $text, string $fromLanguageCode, string $toLanguageCode): string
    {
        if ($text) {
            $response = self::sendYandexPostRequest(
                'https://translate.api.cloud.yandex.net/translate/v2/translate', [
                    "sourceLanguageCode" => $fromLanguageCode,
                    "targetLanguageCode" => $toLanguageCode,
                    "format" => "PLAIN_TEXT",
                    "texts" => [$text]
                ]
            );
            if ($response) {
                $parsedResponse = json_decode($response);
                $text = $parsedResponse->translations[0]->text;
            }
        }

        return $text;
    }

    public static function sendYandexPostRequest(string $url, array $post)
    {
        $apiKey = Config::get('YANDEX_TRANSLATE_API_KEY');
        $ch = curl_init($url);
        $post = json_encode($post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Api-Key ' . $apiKey]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection

        return $result;
    }

    public static function sendYandexGetRequest(string $url, array $data = [])
    {
        $data['key'] = Config::get('YANDEX_TRANSLATE_API_KEY');
        Debug::dump($data['key']);exit;
        $ch = curl_init($url . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection

        return $result;
    }

    public static function getLanguageCodes()
    {
        $content = file_get_contents(Directory::getRootDirectory() . '/codes.html');
        $crawler = new Crawler($content);
        foreach ($crawler->filter('tr') as $tr) {

        }
    }
}