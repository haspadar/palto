<?php
namespace Palto\Layout;

use Palto\Directory;
use Palto\Dispatcher\Dispatcher;
use Palto\Translates;
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

        require Directory::getLayoutsDirectory()
            . '/'
            . $this->getDispatcher()->getModuleName()
            . '/partials/'
            . $file;
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