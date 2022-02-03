<?php
namespace Palto\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Palto\Translates;

class Translate implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('translate', [$this, 'getObject']);
    }

    public function getObject(): self
    {
        return $this;
    }

    public function get(string $name)
    {
        return Translates::get($name, null);
    }

    public function placeholders()
    {

    }
}