<?php
namespace Palto\Dispatcher;

use Palto\Debug;
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

    public function getPutParameter(string $name): string
    {
        parse_str(file_get_contents("php://input"),$put);

        return $put[$name] ?? '';
    }

    public function getModuleName(): string
    {
        $class = strtolower(get_called_class());
        $parts = array_values(array_filter(explode('\\', $class)));

        return $parts[count($parts) - 1];
    }

    public function redirect(string $url)
    {
        header("Location: " . $url,true,301);
    }
}