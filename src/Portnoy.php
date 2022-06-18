<?php

namespace Palto;

use League\CLImate\CLImate;

class Portnoy
{
    public static function run()
    {
        $options = self::prompt();
        Install::run($options['database_username'], $options['database_password']);
        self::setOptions($options);
    }

    private static function prompt(): array
    {
//        $databaseProject = self::getDatabaseProject('/Users/haspadar/Projects');
        $databaseProject = self::getDatabaseProject('/var/www');
        $copyEnvContent = $databaseProject ? file_get_contents($databaseProject . '/configs/.env') : '';
        $regionTitle = self::getRegionTitle();
        $htmlLang = self::getHtmlLang();
        $translateFile = self::getTranslateFile($htmlLang);
        $helpLogin = self::getHelpLogin();
        $helpPassword = self::getHelpPassword();
        while (!self::isHelpCredentialsValid($helpLogin, $helpPassword)) {
            self::showHelpError();
            $helpLogin = self::getHelpLogin();
            $helpPassword = self::getHelpPassword();
        }

        $parserProjectPath = self::getParser($copyEnvContent);
        $layoutTheme = self::getLayoutTheme();

        return [
            'html_lang' => $htmlLang,
            'region_title' => $regionTitle,
            'translate_file' => $translateFile,
            'help_login' => $helpLogin,
            'help_password' => $helpPassword,
            'parser' => $parserProjectPath,
            'database_username' => $copyEnvContent ? self::extractEnvValue('DB_USER', $copyEnvContent) : '',
            'database_password' => $copyEnvContent ? self::extractEnvValue('DB_PASSWORD', $copyEnvContent) : '',
            'layout_theme' => $layoutTheme
        ];
    }

    private static function getWelcomeText(): string
    {
        $now = new \DateTime();
        if ($now >= new \DateTime('06:00') && $now < new \DateTime('12:00')) {
            return 'Доброе утро';
        }

        if ($now >= new \DateTime('12:00') && $now < new \DateTime('12:00')) {
            return 'Добрый день';
        }

        return 'Добрый вечер';
    }

    private static function getHtmlLang(): string
    {
        $climate = new CLImate();
        $defaultLang = 'ru-RU';
        $input = $climate->cyan()->input("Язык сайта, например, $defaultLang?");
        $input->defaultTo($defaultLang);
        $response = $input->prompt();
        $isValid = self::isLangValid($response);
        while (!$isValid) {
            $input = $climate->cyan()->input("Язык сайта, например, $defaultLang?");
            $response = $input->prompt();
            $isValid = self::isLangValid($response);
        }

        return $response;
    }

    private static function isLangValid(string $htmlLang): bool
    {
        $parts = explode('-', $htmlLang);

        return count($parts) == 2 && strlen($parts[0]) == 2 && strlen($parts[1]) == 2;
    }

    private static function getTranslateFile(string $htmlLang): string
    {
        $parts = explode('_', $htmlLang);
        $isRussianDefault = mb_strtolower($parts[0]) == 'ru';
        $climate = new CLImate();
        $defaultLang = $isRussianDefault ? 'RU' : 'EN';
        $defaultLangMessage = 'RU/EN';
        $input = $climate->cyan()->input("Переводы по умолчанию [$defaultLangMessage]? ");
        $input->defaultTo($defaultLang);
        $response = $input->prompt();
        $isValid = self::isTranslateLangValid($response);
        while (!$isValid) {
            $input = $climate->cyan()->input("Переводы по умолчанию [$defaultLangMessage]? ");
            $response = $input->prompt();
            $isValid = self::isTranslateLangValid($response);
        }

        return 'translates.' . ($response == 'RU' ? 'russian' : 'english') . '.php';
    }

    private static function getRotatorUrl(): string
    {
        $climate = new CLImate();
        $input = $climate->cyan()->input("Ссылка на ротатор?");
        $response = $input->prompt();
        $isValid = self::isRotatorUrlValid($response);
        while (!$isValid) {
            $input = $climate->cyan()->input("Правильная ссылка на ротатор?");
            $response = $input->prompt();
            $isValid = self::isRotatorUrlValid($response);
        }

        return $response;
    }

    private static function getRegionTitle(): string
    {
        $climate = new CLImate();
        $welcomeText = self::getWelcomeText();
        $input = $climate->cyan()->input($welcomeText . "! Какой регион парсим (All)?");
        $response = $input->prompt();
        $isValid = (bool)$response;
        while (!$isValid) {
            $input = $climate->cyan()->input("Какой регион парсим (All)?");
            $response = $input->prompt();
            $isValid = (bool)$response;
        }

        return $response;
    }

    private static function isRotatorUrlValid(string $url): bool
    {
        parse_str(parse_url($url)['query'] ?? '', $params);

        return isset($params['key']) && $params['key'];
    }

    private static function isTranslateLangValid($response): bool
    {
        return in_array(mb_strtoupper($response), ['RU', 'EN']);
    }

    private static function isDonorUrlValid($response): bool
    {
        return $response && filter_var($response, FILTER_VALIDATE_URL);
    }

    private static function getHelpLogin(): string
    {
        $climate = new CLImate();
        $input = $climate->cyan()->input("Логин от справки palto.rotator.dev?");
        $response = $input->prompt();
        $isValid = (bool)$response;
        while (!$isValid) {
            $input = $climate->cyan()->input("Логин от справки palto.rotator.dev?");
            $response = $input->prompt();
            $isValid = $response;
        }

        return $response;
    }

    private static function getHelpPassword(): string
    {
        $climate = new CLImate();
        $input = $climate->cyan()->password("Пароль от справки palto.rotator.dev?");
        $response = $input->prompt();
        $isValid = (bool)$response;
        while (!$isValid) {
            $input = $climate->cyan()->password("Пароль от справки palto.rotator.dev?");
            $response = $input->prompt();
            $isValid = $response;
        }

        return $response;
    }

    private static function isHelpCredentialsValid(string $login, string $password): bool
    {
        $ch = self::getHelpCurl($login, $password);
        curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $statusCode == 200;
    }

    private static function getHelpKeys(string $login, string $password): array
    {
        $curlHandle = self::getHelpCurl($login, $password);
        $response = curl_exec($curlHandle);

        return json_decode($response, JSON_OBJECT_AS_ARRAY);
    }

    private static function getHelpCurl(string $login, string $password): \CurlHandle|bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://palto.rotator.dev/keys.php");
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");

        return $ch;
    }

    private static function showHelpError()
    {
        $climate = new CLImate();
        $climate->red()->out("Логин и пароль не подошли");
    }

    private static function getParser(string $copyEnvContent = ''): string
    {
        if ($copyEnvContent) {
            $defaultValue = self::extractEnvValue('PARSE_ADS_SCRIPT', $copyEnvContent);
            if ($defaultValue) {
                return $defaultValue;
            }
        }

        $directories = Directory::getFilesWithDirectories(Directory::getParsersDirectory() . '/ads');
        $parsers = [];
        foreach ($directories as $directory) {
            $parsers = array_merge(
                $parsers,
                array_map(fn($parser) => $directory . '/' . $parser,
                    Directory::getFilesWithDirectories(Directory::getParsersDirectory() . '/ads/' . $directory)
                )
            );
        }

        if ($parsers) {
            $climate = new CLImate();
            $input = $climate->cyan()->radio('Какой парсер используем (например, olx/adsid.php)? ', $parsers);

            return $input->prompt();
        }

        return '';
    }

    private static function setOptions(array $options)
    {
        self::setParser($options['parser']);
        self::setTranslateFile($options['translate_file']);
        self::setHtmlLang($options['html_lang']);
        self::setHelpOptions($options['help_login'], $options['help_password']);
        self::setLayoutTheme($options['layout_theme']);
    }

    private static function setParser(string $parser)
    {
        if ($parser) {
            Logger::debug('Set parser ' . $parser);
            Config::replace('PARSE_ADS_SCRIPT', $parser, Directory::getConfigsDirectory() . '/.env');
            Config::replace('PARSE_CATEGORIES_SCRIPT', $parser, Directory::getConfigsDirectory() . '/.env');
        }
    }

    private static function setTranslateFile(string $translateFile)
    {
        Logger::debug('Set translate file ' . $translateFile);
        file_put_contents(
            Directory::getConfigsDirectory() . '/translates.php',
            file_get_contents(Directory::getConfigsDirectory() . '/' . $translateFile)
        );
    }

    private static function setHtmlLang(string $htmlLang)
    {
        Logger::debug('Set Html Lang to ' . $htmlLang);
        Translates::replace('html_lang', $htmlLang);
    }

    private static function setLayoutTheme(string $layoutTheme)
    {
        Config::replace('LAYOUT_THEME', $layoutTheme, Directory::getConfigsDirectory() . '/.layouts');
    }

    private static function setHelpOptions(string $helpLogin, string $helpPassword)
    {
        $helpKeys = self::getHelpKeys($helpLogin, $helpPassword);
        Logger::debug('Set help keys');
        Config::replace('ROTATOR_URL', $helpKeys['rotator_url'], Directory::getConfigsDirectory() . '/.pylesos');
        Config::replace('SUNDUK_URL', $helpKeys['sunduk_url'], Directory::getConfigsDirectory() . '/.env');
        Config::replace('YANDEX_TRANSLATE_API_KEY', $helpKeys['yandex_translate_api_key'], Directory::getConfigsDirectory() . '/.env');
        Config::replace('SMTP_EMAIL', $helpKeys['smtp_email'], Directory::getConfigsDirectory() . '/.env');
        Config::replace('SMTP_PASSWORD', $helpKeys['smtp_password'], Directory::getConfigsDirectory() . '/.env');
        Config::replace('SMTP_FROM', $helpKeys['smtp_from'], Directory::getConfigsDirectory() . '/.env');
    }

    private static function getDatabaseProject(string $path): string
    {
        $projects = Directory::getPaltoDirectories($path);
        if ($projects) {
            $climate = new CLImate();
            $input = $climate->cyan()->radio('Какую базу данных подключить? ', array_merge(['Новую'], $projects));
            $response = $input->prompt();

            return $response == 'Новую' ? '' : $path . '/' . $response;
        }

        return '';
    }

    private static function extractEnvValue(string $name, string $content)
    {
        $defaultValueStart = mb_strpos($content, $name . '=');
        $defaultValueFinish = mb_strpos($content, PHP_EOL, $defaultValueStart);
        if ($defaultValueStart !== false) {
            $skipLength = strlen($name . '=');

            return str_replace(
                '"',
                '',
                mb_substr($content, $defaultValueStart + $skipLength, $defaultValueFinish - $defaultValueStart - $skipLength)
            );
        }
    }

    private static function getLayoutTheme()
    {
        $climate = new CLImate();
        $input = $climate->cyan()->radio('Какую тему используем? ', ['laspot', 'bootstrap']);

        return $input->prompt();
    }
}