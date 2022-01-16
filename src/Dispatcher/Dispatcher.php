<?php
namespace Palto\Dispatcher;

use Palto\Router\Router;

abstract class Dispatcher
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    abstract public function run();
}