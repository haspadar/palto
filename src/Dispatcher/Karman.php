<?php
namespace Palto\Dispatcher;

use Palto\Auth;
use Palto\Debug;
use Palto\Layouts;

class Karman extends Dispatcher
{
    private const INDEX_ACTION_PATH = 'index';

    private const GET_ACTION_PATH = 'get';

    public function run()
    {
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);
        Auth::check();
        if (!$this->getControllerName()) {
            $this->redirect('/' . $this->getModuleName() . '/complaints');
        } elseif ($this->getRouter()->isAjax()) {
            $controllerName = $this->createCamelCase($this->getControllerName());
            $controller = new ('\Palto\Controller\\' . $controllerName)($this);
            $actionName = lcfirst($this->createCamelCase($this->getActionName()));
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($controller->{$actionName}());
        } else {
            $layout = Layouts::create($this);
            $layout->load($this->getLayoutName());
        }
    }

    private function getControllerName(): string
    {
        $parts = $this->getRouter()->getUrl()->getParts();

        return strtolower($parts[1] ?? '');
    }

    private function createCamelCase(string $name): string
    {
        $parts = explode('-', $name);

        return implode('', array_map(fn($part) => ucfirst($part), $parts));
    }

    private function getActionName(): string
    {
        $parts = $this->getRouter()->getUrl()->getParts();

        return strtolower($parts[2] ?? '');
    }

    private function getLayoutName(): string
    {
        $actionNamePath = $this->getActionName();
        $hasIdQueryParameter = $this->getRouter()->getQueryParameter('id');

        return $this->getModuleName()
            . '/'
            . $this->getControllerName()
            . '/'
            . ($actionNamePath ? $actionNamePath : ($hasIdQueryParameter ? 'get' : 'index'))
            . '.php';
    }
}