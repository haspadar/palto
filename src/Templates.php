<?php

namespace Palto;

use Palto\Controller\Client;
use Palto\Controller\Karman;

class Templates
{
    public static function getThemes(): array
    {
        return array_values(array_filter(
            Directory::getFilesWithDirectories(Directory::getTemplatesDirectory()),
            fn($directory) => $directory != 'karman'
        ));
    }

    public static function getFunctions(): array
    {
        return array_filter(
            get_class_methods(Client::class),
            fn (string $function) => mb_substr($function, 0, 4) == 'show'
        );
    }

    public static function getTemplates(): array
    {
        return array_map(
            fn(array $template) => new Template($template),
            (new Model\Templates())->getTemplates()
        );
    }

    public static function getById(int $id): Template
    {
        return new Template((new Model\Templates())->getById($id));
    }
}