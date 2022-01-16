<?php

namespace Palto\Controller;

class Status extends Controller
{
    public function enableSite()
    {
        \Palto\Status::enableSite();

        return ['success' => true];
    }

    public function disableSite()
    {
        \Palto\Status::disableSite();

        return ['success' => true];
    }

    public function enableCache()
    {
        \Palto\Status::enableCache();

        return ['success' => true];
    }

    public function disableCache()
    {
        \Palto\Status::disableCache();

        return ['success' => true];
    }
}