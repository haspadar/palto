<?php

namespace Palto\Controller;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Palto\Ads;
use Palto\Category;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Filter;
use Palto\Flash;
use Palto\Regions;
use Palto\Url;

class Karman
{
    protected Engine $templatesEngine;
    protected Url $url;

    public function __construct()
    {
        \Palto\Auth::check();
        $this->templatesEngine = new Engine(Directory::getRootDirectory() . '/templates/karman');
        $this->templatesEngine->loadExtension(new Asset(Directory::getPublicDirectory(), false));
        $this->url = new Url();
        $this->templatesEngine->addData([
            'flash' => Flash::receive(),
            'url' => $this->url
        ]);
    }

    public function showStatus()
    {
        $this->templatesEngine->addData([
            'title' => 'Статус',
            'breadcrumbs' => [['title' => 'Статус']]
        ]);
        echo $this->templatesEngine->make('status');
    }

    public function showComplaints()
    {
        $this->templatesEngine->addData([
            'complaints' => \Palto\Complaints::getActualComplaints(),
            'title' => 'Жалобы',
            'breadcrumbs' => [['title' => 'Жалобы']]
        ]);
        echo $this->templatesEngine->make('complaints');
    }

    public function showComplaint(int $id)
    {
        $complaint = \Palto\Complaints::getComplaint($id);
        $this->templatesEngine->addData([
            'complaint' => $complaint,
            'ad' => \Palto\Ads::getById($complaint['id']),
            'title' => 'Жалоба #' . $complaint['id'],
            'breadcrumbs' => [[
                'title' => 'Жалобы',
                'url' => '/karman/complaints?cache=0'
            ], ['title' => 'Жалоба #' . $complaint['id']]]
        ]);
        echo $this->templatesEngine->make('complaint');
    }

    public function ignoreComplaint(int $id)
    {
        if ($id) {
            \Palto\Complaints::ignoreComplaint($id);
            $complaint = \Palto\Complaints::getComplaint($id);
            $ad = Ads::getById($complaint['ad_id']);
            Flash::add(json_encode([
                'message' => 'Жалоба #' . $id . ' на <a href="' . $ad->generateUrl() . '" target="_blank">объявление</a> проигнарировано',
                'type' => 'success'
            ]));
            $this->showJsonResponse(['success' => true]);
        } else {
            $this->showJsonResponse(['error' => 'Не указан ID']);
        }
    }

    public function ignoreComplaints()
    {
        $idsString = Filter::get($this->getPutParams()['ids'] ?? '');
        $ids = Filter::getIntArray(explode(',', $idsString));
        if ($ids) {
            \Palto\Complaints::ignoreComplaints($ids);
            Flash::add(json_encode([
                'message' => 'Жалобы ##' . $idsString . ' проигнарированы',
                'type' => 'success'
            ]));
            $this->showJsonResponse(['success' => true]);
        } else {
            $this->showJsonResponse(['error' => 'Не указан ID']);
        }
    }

    public function removeAd(int $id)
    {
        if ($id) {
            try {
                \Palto\Complaints::removeAd($id);
                $complaint = \Palto\Complaints::getComplaint($id);
                $ad = Ads::getById($complaint['ad_id']);
                Flash::add(json_encode([
                    'message' => '<a href="' . $ad->generateUrl() . '" target="_blank">Объявление</a> с жалобой #' . $id . ' удалено',
                    'type' => 'success'
                ]));
                $this->showJsonResponse(['success' => true]);
            } catch (\Exception $e) {
                $this->showJsonResponse(['error' => $e->getTraceAsString()]);
            }
        } else {
            $this->showJsonResponse(['error' => 'Не указан ID']);
        }
    }

    public function removeAds()
    {
        $idsString = Filter::get($this->getPutParams()['ids'] ?? '');
        $ids = Filter::getIntArray(explode(',', $idsString));
        if ($ids) {
            \Palto\Complaints::removeAds($ids);
            Flash::add(json_encode([
                'message' => 'Объявления с жалобами ##' . $idsString . ' удалены',
                'type' => 'success'
            ]));
            $this->showJsonResponse(['success' => true]);
        } else {
            $this->showJsonResponse(['error' => 'Не указан ID']);
        }
    }

    public function showCategory(int $id)
    {
        $category = \Palto\Categories::getById($id);
        $parents = $category->getParents();
        $parentsUrls = array_map(fn(Category $parent) => [
            'title' => $parent->getTitle(),
            'url' => '/karman/categories/' . $parent->getId()
        ], $parents);
        $this->templatesEngine->addData([
            'title' => 'Категория',
            'category' => $category,
            'categories' => \Palto\Categories::getLiveCategories($category),
            'breadcrumbs' => array_merge([[
                'title' => 'Категории',
                'url' => '/karman/categories?cache=0'
            ]], $parentsUrls, [[
                'title' => 'Категория "' . $category->getTitle() . '"',
            ]])
        ]);
        echo $this->templatesEngine->make('category');
    }

    public function showCategories()
    {
        $this->templatesEngine->addData([
            'title' => 'Категории',
            'breadcrumbs' => [],
            'categories' => \Palto\Categories::getLiveCategories()
        ]);
        echo $this->templatesEngine->make('categories');
    }

    public function updateCategory(int $id)
    {
        $title = Filter::get($this->getPutParams()['title']);
        \Palto\Categories::update([
            'title' => $title,
            'url' => Filter::get($this->getPutParams()['url']),
            'emoji' => Filter::get($this->getPutParams()['emoji'])
        ], $id);
        Flash::add(json_encode([
            'message' => 'Категория <a href="/karman/categories/' . $id . '">"' . $title . '"</a> обновлена',
            'type' => 'success'
        ]));
        $this->showJsonResponse(['success' => true]);
    }

    public function removeEmoji(int $id)
    {
        \Palto\Categories::update([
            'emoji' => ''
        ], $id);
        Flash::add(json_encode([
            'message' => 'Emoji удалена',
            'type' => 'success'
        ]));
        $this->showJsonResponse(['success' => true]);
    }

    public function disableSite()
    {
        \Palto\Status::disableSite();
        $this->showJsonResponse(['success' => true]);
    }

    public function enableSite()
    {
        \Palto\Status::enableSite();
        $this->showJsonResponse(['success' => true]);
    }

    public function disableCache()
    {
        \Palto\Status::disableCache();
        $this->showJsonResponse(['success' => true]);
    }

    public function enableCache()
    {
        \Palto\Status::enableCache();
        $this->showJsonResponse(['success' => true]);
    }

    private function getPutParams(): array
    {
        parse_str(file_get_contents("php://input"),$params);

        return $params;
    }

    private function showJsonResponse(array $data = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}