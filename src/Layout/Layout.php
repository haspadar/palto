<?php
namespace Palto\Layout;

use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Dispatcher\Dispatcher;
use Palto\Url;

abstract class Layout
{
    protected Dispatcher $dispatcher;

    protected string $name;

    protected array $partialVariables;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function load(string $name)
    {
        require_once Directory::getLayoutsDirectory() . '/' . $name;
    }

    public static function getTheme(): string
    {
        if (self::getDispatcher()->getModuleName() == 'karman') {
            return 'karman';
        }


    }

    public static function getThemes(): array
    {
        return array_map(
            fn($directory) => substr($directory, 0, -6),
            Directory::getThemes()
        );
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): Url
    {
        return $this->getDispatcher()->getRouter()->getUrl();
    }

    public function partial(string $file, array $variables = [])
    {
        $this->partialVariables = $variables;
        $theme = $this->getDispatcher()->getTheme();

        require Directory::getLayoutsDirectory() . '/' . $theme . '/partials/' . $file;
    }

    public function getPartialVariable(string $name)
    {
        return $this->partialVariables[$name] ?? '';
    }

    public function getParameter(string $name): string
    {
        return $this->getDispatcher()->getRouter()->getQueryParameter($name);
    }
}