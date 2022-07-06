<?php

namespace Palto;

class Counters
{
    /**
     * @var null[]|string[]
     */
    private static array $counters;

    public static function get(string $name)
    {
        $counters = self::getCounters();
        $counter = $counters[$name] ?? '';
        $value = $counter['value'] ?: '';

        return $counter['type'] == 'codes' ? explode(PHP_EOL, $value) : $value;
    }

    public static function receive(string $name)
    {
        $counters = self::getCounters();
        if (isset($counters[$name])) {
            $counter = is_array($counters[$name]) && $counters[$name]
                ? array_shift($counters[$name])
                : '';
            self::$counters[$name] = $counters[$name] ?? [];
            $value = $counter['value'] ?: '';

            return $counter['type'] == 'codes' ? explode(PHP_EOL, $value) : $value;
        }

        return '';
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
                'google_search' => [$googleSearchStart, 0, $googleEnd],
                'google_header' => ['<?php endforeach;?>
    <?php endif;?>
    ', 0, '</head>']
            ]
        ];

        $counters = [];
        foreach ($patterns as $file => $fileReplaces) {
            foreach ($fileReplaces as $translateKey => $fileReplace) {
                if ($fileReplace) {
                    $counters[$translateKey] = Translates::extractLayoutTranslate($fileReplace[0], $fileReplace[1], $fileReplace[2], self::getLayoutsDirectory() . '/client/' . $file);
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
    'google_header' => '" . ($extractedCounters['google_header'] ?? '') . "',
    'google' => '" . ($extractedCounters['google'] ?? '') . "',
    'google_auto' => '" . ($extractedCounters['google_auto'] ?? '') . "',
    'google_search' => '" . ($extractedCounters['google_search'] ?? '') . "',
];";
        file_put_contents($fileName, $content);
    }

    private static function getCounters(): array
    {
        if (!isset(self::$counters)) {
            self::$counters = Settings::getCounters();
        }

        return self::$counters;
    }

    private static function getLayoutsDirectory(): string
    {
        return Directory::getRootDirectory() . '/layouts';
    }
}