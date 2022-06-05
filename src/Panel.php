<?php

namespace Palto;

class Panel
{
    public static function getComplaintsCount(): int
    {
        return \Palto\Complaints::getActualComplaintsCount();
    }

    public static function isSiteEnabled(): bool
    {
        return Status::isSiteEnabled();
    }

    public static function isCacheEnabled(): bool
    {
        return Status::isCacheEnabled();
    }

    public static function getBusySpace(): string
    {
        return \Palto\Status::getDirectoryUsePercent('/');
    }

    public static function isPanelEnabled(): bool
    {
        $isDisabled = ($_GET['panel'] ?? 1) == 0;

        return \Palto\Auth::isLogged() &&  !$isDisabled;
    }

    public static function getHideUrl(): Url
    {
        return (new \Palto\Url())->generate(['panel' => 0]);
    }
}