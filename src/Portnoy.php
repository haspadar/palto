<?php

namespace Palto;

use League\CLImate\CLImate;

class Portnoy
{
    public static function run()
    {
        $options = self::prompt();
        Install::run();
        self::setOptions($options);
    }

    private static function prompt(): array
    {
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

        $parserProjectPath = self::getParserProjectPath();

        return [
            'html_lang' => $htmlLang,
            'region_title' => $regionTitle,
            'translate_file' => $translateFile,
            'help_login' => $helpLogin,
            'help_password' => $helpPassword,
            'parser_project_path' => $parserProjectPath
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

    private static function getParserProjectPath(): string
    {
        $path = '/Users/haspadar/Projects';
        $projects = Directory::getPaltoDirectories($path);
        if ($projects) {
            $climate = new CLImate();
            $input = $climate->cyan()->radio('Откуда скопировать парсеры? ', $projects);
            $response = $input->prompt();

            return $path . '/' . $response;
        }

        return '';
    }

    private static function setOptions(array $options)
    {
        self::setParserProject($options['parser_project_path']);
        self::setTranslateFile($options['translate_file']);
        self::setHtmlLang($options['html_lang']);
        self::setHelpOptions($options['help_login'], $options['help_password']);
    }

    private static function setParserProject(string $parserProjectPath)
    {
        if ($parserProjectPath) {
            Logger::debug('Set parser project ' . $parserProjectPath);
            file_put_contents(
                Directory::getParseCategoriesScript(),
                file_get_contents(Directory::getParseCategoriesScript())
            );
            file_put_contents(
                Directory::getParseAdsScript(),
                file_get_contents(Directory::getParseAdsScript())
            );
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
}