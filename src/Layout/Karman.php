<?php

namespace Palto\Layout;

class Karman extends Layout
{

    public function getId(): int
    {
        return $this->getDispatcher()->getRouter()->getQueryParameter('id');
    }
}