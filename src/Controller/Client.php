<?php

namespace Palto\Controller;

use League\Plates\Engine;
use Palto\Debug;
use Palto\Plates\Extension\Translate;

class Client
{
    public function __construct()
    {
        $templatesEngine = new Engine(\Palto\Directory::getRootDirectory() . '/templates');
        $templatesEngine->loadExtension(new Translate());


        Debug::dump('Constructor');
    }

    public function index($p1)
    {
        Debug::dump('Hello');
    }

    public function region($p1, $p2)
    {
        Debug::dump($p1);
        Debug::dump($p2);
        Debug::dump('Category');
    }

    public function category()
    {

    }

    public function notFound()
    {
        header('HTTP/1.1 404 Not Found');
        Debug::dump('Not');
    }
}