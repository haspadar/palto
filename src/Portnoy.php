<?php

namespace Palto;

use League\CLImate\CLImate;

class Portnoy
{
    public static function run()
    {
        $options = self::prompt();
        Install::run($options);
        self::setOptions($options);
    }

    private static function prompt(): array
    {
//        $donorDatabaseProject = self::getDatabaseProject('/Users/haspadar/Projects');
        $donorDatabaseProject = self::getDatabaseProject('/var/www');
        $regionTitle = self::getRegionTitle();
        $htmlLang = self::getHtmlLang();
        $helpLogin = self::getHelpLogin();
        $helpPassword = self::getHelpPassword();
        while (!self::isHelpCredentialsValid($helpLogin, $helpPassword)) {
            self::showHelpError();
            $helpLogin = self::getHelpLogin();
            $helpPassword = self::getHelpPassword();
        }

        $adsParserProjectPath = self::getAdsParser();
        $templateTheme = self::getTemplateTheme();

        return [
            'html_lang' => $htmlLang,
            'region_title' => $regionTitle,
            'help_login' => $helpLogin,
            'help_password' => $helpPassword,
            'ads_parser' => $adsParserProjectPath,
            'database_username' => $donorDatabaseProject
                ? self::extractEnvValue('DB_USER', file_get_contents($donorDatabaseProject . '/configs/.env'))
                : '',
            'database_password' => $donorDatabaseProject
                ? self::extractEnvValue('DB_PASSWORD', file_get_contents($donorDatabaseProject . '/configs/.env'))
                : '',
            'template_theme' => $templateTheme
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
        $defaultLang = 'en-US';
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

    private static function getAdsParser(): string
    {
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
        self::setAdsParser($options['ads_parser']);
        self::setHtmlLang($options['html_lang']);
        self::setHelpOptions($options['help_login'], $options['help_password']);
        self::setTemplateTheme($options['template_theme']);
    }

    private static function setAdsParser(string $parser)
    {
        if ($parser) {
            Logger::debug('Set ads parser ' . $parser);
            Settings::updateByName('ads_parser', $parser);
        }
    }

    private static function setHtmlLang(string $htmlLang)
    {
        Logger::debug('Set Html Lang to ' . $htmlLang);
        Translates::updateByName('html_lang', $htmlLang);
    }

    private static function setTemplateTheme(string $templateTheme)
    {
        Settings::updateByName('template_theme', $templateTheme);
    }

    private static function setHelpOptions(string $helpLogin, string $helpPassword)
    {
        $helpKeys = self::getHelpKeys($helpLogin, $helpPassword);
        Logger::debug('Set help keys');
        Config::replace('ROTATOR_URL', $helpKeys['rotator_url'], Directory::getConfigsDirectory() . '/.pylesos');
        Settings::updateByName('sunduk_url', $helpKeys['sunduk_url']);
        Settings::updateByName('yandex_translate_api_key', $helpKeys['yandex_translate_api_key']);
        Settings::updateByName('smtp_email', $helpKeys['smtp_email']);
        Settings::updateByName('smtp_password', $helpKeys['smtp_password']);
        Settings::updateByName('smtp_from', $helpKeys['smtp_from']);
    }

    private static function getDatabaseProject(string $path): string
    {
        $projects = Directory::getPaltoDirectories($path);
        if ($projects) {
            $climate = new CLImate();
            $new = 'Новую, отдельную';
            $old = 'Старую, общую';
            $input = $climate->cyan()->radio('Какую базу данных подключить?', [$new, $old]);
            $response = $input->prompt();
            if ($response == $new) {
                return '';
            } else {
                do {
                    $input = $climate->cyan()->radio('Выберите общую базу', $projects);
                    $response = $input->prompt();
                } while (!$response);

                return $response;
            }
        }

        return '';
    }

    private static function extractEnvValue(string $name, string $content): string
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

        return '';
    }

    private static function getTemplateTheme()
    {
        $climate = new CLImate();
        $input = $climate->cyan()->radio('Какую тему используем? ', ['laspot', 'laspot-div']);

        return $input->prompt();
    }

    private static function getCopyDatabaseConnection(string $copyEnvContent): ?\MeekroDB
    {
        return $copyEnvContent
            ? new \MeekroDB(
                self::extractEnvValue('DB_HOST', $copyEnvContent) ?? '127.0.0.1',
                self::extractEnvValue('DB_USER', $copyEnvContent),
                self::extractEnvValue('DB_PASSWORD', $copyEnvContent),
                self::extractEnvValue('DB_NAME', $copyEnvContent),
                self::extractEnvValue('DB_PORT', $copyEnvContent) ?? 3306,
                'utf8mb4'
            ) : null;
    }
}