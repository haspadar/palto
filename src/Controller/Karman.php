<?php

namespace Palto\Controller;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Palto\Config;
use Palto\Directory;
use Palto\Flash;
use Palto\Regions;
use Palto\Url;

class Karman
{
    protected Engine $templatesEngine;
    protected Url $url;

    public function __construct()
    {
        $this->templatesEngine = new Engine(Directory::getRootDirectory() . '/templates/karman');
        $this->templatesEngine->loadExtension(new Asset(Directory::getPublicDirectory(), false));
        $this->url = new Url();
        $this->templatesEngine->addData([
            'flash' => Flash::receive(),
            'url' => $this->url
        ]);
    }

    public function showComplaints()
    {
        $this->templatesEngine->addData([
            'complaints' => \Palto\Complaints::getActualComplaints(),
            'title' => 'Жалобы',
            'breadcrumbs' => []
        ]);
        echo $this->templatesEngine->make('complaints');
    }
}