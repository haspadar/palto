<?php

namespace Palto\Controller;

use Palto\Dispatcher\Dispatcher;

abstract class Controller
{
    protected Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    public function index()
    {

    }
}