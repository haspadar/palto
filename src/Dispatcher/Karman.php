<?php
namespace Palto\Dispatcher;

use Palto\Auth;
use Palto\Layouts;

class Karman extends Dispatcher
{
    public function run()
    {
        Auth::check();
        $layout = Layouts::create($this);
        $layout->load();
    }
}