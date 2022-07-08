<?php

namespace Palto;

class Settings
{
    public static function isKarmanPanelEnabled(): bool
    {
        return \Palto\Auth::isLogged() && self::getByName('is_karman_panel_enabled') == 1;
    }

    public static function getValues(): array
    {
        return (new \Palto\Model\Settings())->getAll();
    }

    public static function getByName(string $name)
    {
        return (new \Palto\Model\Settings())->getByName($name)['value'];
    }

    public static function getById(int $id)
    {
        return (new \Palto\Model\Settings())->getById($id);
    }

    public static function updateByName(string $name, string $value)
    {
        (new \Palto\Model\Settings())->updateByName($name, $value);
    }

    public static function update(array $setting, int $id)
    {
        (new \Palto\Model\Settings())->update($setting, $id);
    }

    public static function isAuthEnabled(): bool
    {
        return self::getByName('is_auth_enabled') == 1 && !IP::isLocal();
    }

    public static function isDonorUrlEnabled(): bool
    {
        return self::getByName('is_donor_url_enabled') == 1;
    }

    public static function isYoutubeUrlEnabled(): bool
    {
        return self::getByName('is_youtube_url_enabled') == 1;
    }

    public static function isHotTemplateEnabled(): bool
    {
        return self::getByName('is_hot_template_enabled') == 1;
    }

    public static function getCounters(): array
    {
        $settings = (new \Palto\Model\Settings())->getByGroup('Коды');
        $grouped = [];
        foreach ($settings as $setting) {
            $grouped[$setting['name']] = $setting;
        }

        return $grouped;
    }
}