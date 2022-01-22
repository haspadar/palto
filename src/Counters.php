<?php

namespace Palto;

class Counters
{
    /**
     * @var null[]|string[]
     */
    private static array $counters;

    public static function get(string $name): string
    {
        $counters = self::getCounters();
        $counter = $counters[$name] ?? '';

        return $counter ?: $name;
    }

    public static function extractCounters(): array
    {
        $googleAutoStart = '<script async src="https://pagead2.googlesyndication.com';
        $googleSearchStart = '<script async src="https://cse.google.com/cse.js';
        $googleEnd = '</script>';
        $patterns = [
            'partials/footer.inc' => [
                'liveinternet' => ['<!--LiveInternet counter-->', 0, '<!--/LiveInternet-->'],
            ],
            'list.php' => [
                'google' => ['</h1>', 0, '<?php if ($flashMessage)']
            ],
            'partials/header.inc' => [
                'google_auto' => [$googleAutoStart, 0, $googleEnd],
                'google_search' => [$googleSearchStart, 0, $googleEnd]
            ]
        ];

        $counters = [];
        foreach ($patterns as $file => $fileReplaces) {
            foreach ($fileReplaces as $translateKey => $fileReplace) {
                if ($fileReplace) {
                    $counters[$translateKey] = Translates::extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], \Palto\Directory::getLayoutsDirectory() . '/client/' . $file);
                }
            }
        }

        if ($counters['google_auto']) {
            $counters['google_auto'] = $googleAutoStart . $counters['google_auto'] . $googleEnd;
        }

        if ($counters['google_search']) {
            $counters['google_search'] = $googleSearchStart . $counters['google_search'] . $googleEnd;
        }

        return $counters;
    }

    public static function saveCounters($extractedCounters, $fileName)
    {
        $content = "<?php
return [
    'liveinternet' => '" . ($extractedCounters['liveinternet'] ?? '') . "',
    'google' => '" . ($extractedCounters['google'] ?? '') . "',
    'google_auto' => '" . ($extractedCounters['google_auto'] ?? '') . "',
    'google_search' => '" . ($extractedCounters['google_search'] ?? '') . "',
];";
        file_put_contents($fileName, $content);
    }

    private static function getCounters(): array
    {
        if (!isset(self::$counters)) {
            self::$counters = require_once Directory::getConfigsDirectory() . '/counters.php';
        }

        return self::$counters;
    }
}