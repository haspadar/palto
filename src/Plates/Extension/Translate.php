<?php
namespace Palto\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Palto\Debug;
use Palto\Russian;
use Palto\Translates;

class Translate implements ExtensionInterface
{
    private Engine $engine;

    public function register(Engine $engine)
    {
        $this->engine = $engine;
        $engine->registerFunction('translate', [$this, 'get']);
    }

    public function get(string $name): string
    {
        return Translates::removeExtra(
            Translates::replacePlaceholders(
                Translates::getValue($name),
                $this->engine->getData()['region'],
                $this->engine->getData()['category'],
                $this->engine->getData()['ad'],
            )
        );
    }
}