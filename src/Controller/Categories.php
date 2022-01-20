<?php

namespace Palto\Controller;

use Palto\Ads;
use Palto\Debug;
use Palto\Filter;
use Palto\Flash;

class Categories extends Controller
{
    public function update(): array
    {
        $title = Filter::get($this->getDispatcher()->getPutParameter('title'));
        $id = intval($this->getDispatcher()->getRouter()->getQueryParameter('id'));
        \Palto\Categories::update([
            'title' => $title,
            'url' => Filter::get($this->getDispatcher()->getPutParameter('url')),
            'emoji' => Filter::get($this->getDispatcher()->getPutParameter('emoji'))
        ], $id);
        Flash::add(json_encode([
            'message' => 'Категория <a href="/karman/categories?id=' . $id . '">"' . $title . '"</a> обновлена',
            'type' => 'success'
        ]));

        return ['success' => true];
    }
}