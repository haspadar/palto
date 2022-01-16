<?php
namespace Palto\Layout;

use Palto\Debug;
use Palto\Directory;
use Palto\Dispatcher\Dispatcher;

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

    public function partial(string $file, array $variables = [])
    {
        $this->partialVariables = $variables;

        require Directory::getLayoutsDirectory() . '/client/partials/' . $file;
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