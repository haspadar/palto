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
        $patterns = [
            'partials/footer.inc' => [
                'liveinternet' => ['<!--LiveInternet counter-->', 0, '<!--/LiveInternet-->'],
            ],
            'list.php' => [
                'google' => ['</h1>', 0, '<?php if ($flashMessage)']
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

        return $counters;
    }

    public static function saveCounters($extractedCounters, $fileName)
    {
        $content = "<?php
return [
    'liveinternet' => '" . ($extractedCounters['liveinternet'] ?? '') . "',

    'google' => '" . ($extractedCounters['google'] ?? '') . "'
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