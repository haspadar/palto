<?php

namespace Palto;

class Youtube
{
    public function getVideoId(string $query): string
    {
        $query = str_replace('Cruisecontrol', 'Cruise control', $query);
        $html = $this->download('https://www.youtube.com/results?search_query=' . urlencode($query), $this->getProxy());

        return $this->parseVideoId($html);
    }

    private function parseVideoId($html): string
    {
        $pattern = '/watch?v=';
        $videoUrlStart = strpos($html, '/watch?v=');
        if ($videoUrlStart) {
            $videoUrlFinish = strpos($html, '"', $videoUrlStart);
            $videoId = substr(
                $html,
                $videoUrlStart + strlen($pattern),
                $videoUrlFinish - $videoUrlStart - strlen($pattern)
            );
        }

        return $videoId ?? '';
    }
    
    private function download($url, $proxy)
    {
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = \curl_init();

        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        \curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Upgrade-Insecure-Requests: 1';
        $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
        $headers[] = 'Sec-Fetch-Site: none';
        $headers[] = 'Sec-Fetch-Mode: navigate';
        $headers[] = 'Sec-Fetch-User: ?1';
        $headers[] = 'Sec-Fetch-Dest: document';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,ru;q=0.8,be;q=0.7,uk;q=0.6';
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($proxy && isset($proxy->address) && isset($proxy->auth)) {
            \curl_setopt($ch, CURLOPT_PROXY, $proxy->address);
            \curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy->auth);
        }

        $result = \curl_exec($ch);
        if (\curl_errno($ch)) {
            echo 'Error:' . \curl_error($ch);
        }

        \curl_close($ch);

        return $result;
    }


    private function getProxy()
    {
        $rotatorUrl = Config::get('ROTATOR_URL');
        if ($rotatorUrl) {
            $response = \json_decode(file_get_contents(Config::get('ROTATOR_URL')));
            shuffle($response->list);

            return $response->list[0];
        }

        return null;
    }
}