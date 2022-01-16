<?php
namespace Palto\Dispatcher;

use Palto\Auth;
use Palto\Debug;
use Palto\Layouts;

class Karman extends Dispatcher
{
    private const DEFAULT_CONTROLLER_PATH = 'summary';

    private const DEFAULT_ACTION_PATH = 'index';

    public function run()
    {
        Auth::check();
        if ($this->isAjax()) {
            $controllerName = $this->getControllerName();
            $controller = new ('\Palto\Controller\\' . $controllerName)($this);
            $actionName = $this->getActionName();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($controller->{$actionName}());
        } else {
            $layout = Layouts::create($this);
            $layout->load($this->getLayoutName());
        }
    }

    private function getControllerNamePath(): string
    {
        $parts = $this->getRouter()->getUrl()->getParts();

        return strtolower($parts[1] ?? self::DEFAULT_CONTROLLER_PATH);
    }

    private function getControllerName(): string
    {
        return $this->createCamelCase($this->getControllerNamePath());
    }

    private function createCamelCase(string $name): string
    {
        $parts = explode('-', $name);

        return implode('', array_map(fn($part) => ucfirst($part), $parts));
    }

    private function getActionNamePath(): string
    {
        $parts = $this->getRouter()->getUrl()->getParts();

        return strtolower($parts[2] ?? self::DEFAULT_ACTION_PATH);
    }

    private function getActionName(): string
    {
        return lcfirst($this->createCamelCase($this->getActionNamePath()));
    }

    private function getLayoutName(): string
    {
        return 'karman/' . $this->getControllerNamePath() . '/' . $this->getActionNamePath() . '.php';
    }

    private function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}