<?php

namespace Palto;

use League\CLImate\CLImate;

class Portnoy
{
    public static function run()
    {
        $options = self::prompt();
//        Install::run();
        self::setOptions($options);
    }

    private static function prompt(): array
    {
        $donorUrl = self::getDonorUrl();
        $htmlLang = self::getHtmlLang();
        $translateFile = self::getTranslateFile($htmlLang);
        $regionTitle = self::getRegionTitle();
        $rotatorUrl = self::getRotatorUrl();
        $helpLogin = self::getHelpLogin();
        $helpPassword = self::getHelpPassword();
        while (!self::isHelpCredentialsValid($helpLogin, $helpPassword)) {
            self::showHelpError();
            $helpLogin = self::getHelpLogin();
            $helpPassword = self::getHelpPassword();
        }

        $parserProject = self::getParserProject();

        return [
            'donor_url' => $donorUrl,
            'html_lang' => $htmlLang,
            'region_title' => $regionTitle,
            'translate_file' => $translateFile,
            'rotator_url' => $rotatorUrl,
            'help_login' => $helpLogin,
            'help_password' => $helpPassword,
            'parser_project' => $parserProject
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

    private static function getDonorUrl(): Url
    {
        $climate = new CLImate();
        $welcomeText = self::getWelcomeText();
        $input = $climate->cyan()->input($welcomeText . '! Откуда будем парсить?');
        $response = $input->prompt();
        $isValid = self::isDonorUrlValid($response);
        while (!$isValid) {
            $input = $climate->cyan()->input('Откуда-откуда?');
            $response = $input->prompt();
            $isValid = self::isDonorUrlValid($response);
        }

        return new Url($response);
    }

    private static function getHtmlLang(): string
    {
        $climate = new CLImate();
        $defaultLang = 'ru_RU';
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
        $parts = explode('_', $htmlLang);

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

        return 'translates.' . $response . '.php';
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
        $input = $climate->cyan()->input("Название региона, например, All?");
        $response = $input->prompt();
        $isValid = (bool)$response;
        while (!$isValid) {
            $input = $climate->cyan()->input("Правильное название региона, например, All?");
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
            $isValid = !$response;
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
            $isValid = !$response;
        }

        return $response;
    }

    private static function isHelpCredentialsValid(string $login, string $password): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://palto.rotator.dev/");
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return $statusCode == 200;
    }

    private static function showHelpError()
    {
        $climate = new CLImate();
        $climate->red()->out("Логин и пароль не подошли");
    }

    private static function getParserProject(): string
    {
        $projects = Directory::getPaltoDirectories('/Users/haspadar/Projects');
        if ($projects) {
            $climate = new CLImate();
            $input = $climate->radio('Откуда скопировать парсеры? ', $projects);
            $response = $input->prompt();
        }

        return $response ?? '';
    }

    private static function setOptions(array $options)
    {
//        ROTATOR_URL
//        Directory::getConfigsDirectory() . '/.pylesos';
        file_put_contents(
            Directory::getConfigsDirectory() . '/.pylesos',
            strtr(
                file_get_contents(Directory::getConfigsDirectory() . '/.pylesos'), [
                    'ROTATOR_URL=""' => 'ROTATOR_URL="' . $options['donor_url'] . '"'
                ]
            )
        );

        file_put_contents(
            Directory::getConfigsDirectory() . '/.pylesos',
        )
//        'donor_url' => $donorUrl,
//        'html_lang' => $htmlLang,
//        'translate_file' => $translateFile,
//        'rotator_url' => $rotatorUrl,
//        'help_login' => $helpLogin,
//        'help_password' => $helpPassword,
//        'parser_project' => $parserProject

    }
}