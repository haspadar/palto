<?php

namespace Palto\Controller;

use Exception;
use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Palto\Ads;
use Palto\Auth;
use Palto\Categories;
use Palto\Category;
use Palto\Cli;
use Palto\Config;
use Palto\Debug;
use Palto\Directory;
use Palto\Filter;
use Palto\Flash;
use Palto\Plural;
use Palto\Regions;
use Palto\Synonyms;
use Palto\Url;
use Palto\Validator;

class Karman
{
    protected Engine $templatesEngine;
    protected Url $url;

    public function __construct()
    {
        Auth::check();
        $this->templatesEngine = new Engine(Directory::getKarmanTemplatesDirectory());
        $this->templatesEngine->loadExtension(new Asset(Directory::getPublicDirectory(), false));
        $this->url = new Url();
        $this->templatesEngine->addData([
            'flash' => Flash::receive(),
            'url' => $this->url,
            'undefined_ads_count' => Ads::getUndefinedCount(),
            'undefined_categories' => Categories::getUndefinedAll('level ASC'),
            'actual_complaints_count' => \Palto\Complaints::getActualComplaintsCount()
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
            'ad' => Ads::getById($complaint['id']),
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
                'message' => 'Жалоба #' . $id . ' на <a href="' . $ad->generateUrl() . '" target="_blank">объявление</a> проигнорировано',
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
                'message' => 'Жалобы ##' . $idsString . ' проигнорированы',
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
            } catch (Exception $e) {
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

    public function showUndefinedCategories()
    {
        $undefinedCategories = Categories::getUndefinedAll('level ASC');
        $this->templatesEngine->addData([
            'title' => 'Undefined-категории',
            'categories' => $undefinedCategories,
            'synonyms' => Synonyms::getGropedAll(),
            'ads_counts' => Ads::getCategoriesAdsCounts(array_map(fn(Category $category) => $category->getId(), $undefinedCategories)),
            'breadcrumbs' => [],
        ]);
        echo $this->templatesEngine->make('categories');
    }

    public function showAds(int $id)
    {
        $category = Categories::getById($id);
        $this->templatesEngine->addData([
            'title' => 'Объявления ' . $category->getTitle(),
            'category' => $category,
            'ads' => Ads::getAds(null, $category, 25),
            'breadcrumbs' => array_merge([[
                'title' => 'Категории',
                'url' => '/karman/categories?cache=0'
            ], [
                'title' => $category->getTitle(),
                'url' => '/karman/categories/' . $category->getId() . '?cache=0'
            ]], [[
                'title' => 'Объявления',
            ]]),
            'ads_count' => Ads::getCategoriesAdsCount([$category->getId()])
        ]);
        echo $this->templatesEngine->make('ads');
    }

    public function showCategory(int $id)
    {
        $category = Categories::getById($id);
        $parents = $category->getParents();
        $parentsUrls = array_map(fn(Category $parent) => [
            'title' => $parent->getTitle(),
            'url' => '/karman/categories/' . $parent->getId()
        ], $parents);
        $categories = Categories::getChildren([$category->getId()])[$category->getId()] ?? [];
        $this->templatesEngine->addData([
            'title' => 'Категория',
            'category' => $category,
            'categories' => $categories,
            'ads_counts' => Ads::getCategoriesAdsCounts(
                array_map(fn(Category $category) => $category->getId(), $categories),
                $category->getLevel() + 1
            ),
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
        $categories = Categories::getLiveCategories();
        $this->templatesEngine->addData([
            'title' => 'Категории',
            'breadcrumbs' => [],
            'categories' => $categories,
            'ads_counts' => Ads::getCategoriesAdsCounts(array_map(fn(Category $category) => $category->getId(), $categories), 1),
        ]);
        echo $this->templatesEngine->make('categories');
    }

    public function getCategoriesRoots(): void
    {
        $this->showJsonResponse(array_map(fn(Category $category) => [
            'id' => $category->getId(),
            'parent_id' => $category->getParentId() ?: null,
            'title' => $category->getTitle()
        ], Categories::getRoots()));
    }

    public function moveAd(): void
    {
        $params = $this->getPutParams();
        $error = Validator::validateMoveAd(
            intval($params['category_level_1'] ?? 0),
            Filter::get($params['new_category_level_1'] ?? ''),
            Filter::get($params['new_category_level_2'] ?? ''),
        );
        if ($error) {
            $this->showJsonResponse(['error' => $error]);
        } else {
            $adId = intval($params['ad_id']);
            Ads::moveAd(
                $adId,
                intval($params['category_level_1'] ?? 0),
                Filter::get($params['new_category_level_1'] ?? ''),
                intval($params['category_level_2'] ?? 0),
                Filter::get($params['new_category_level_2'] ?? ''),
            );
            $category = Ads::getById(intval($params['ad_id']))->getCategory();
            $newSynonyms = Filter::getSynonyms($params['synonyms']);
            $error = Validator::validateSynonyms($newSynonyms);
            if (!$error) {
                $category->addSynonyms($newSynonyms);
                Flash::add(json_encode([
                    'message' => "Объявление $adId перемещено в \""
                        . $category->getTitle()
                        . "\". ",
                    'type' => 'success'
                ]));
            }

            $this->showJsonResponse(['success' => true]);
        }
    }

    public function getCategoriesChildren(int $parentId): void
    {
        $children = $parentId ? Categories::getChildren([$parentId]) : [];
        $this->showJsonResponse(array_map(fn(Category $category) => [
            'id' => $category->getId(),
            'parent_id' => $category->getParentId() ?: null,
            'title' => $category->getTitle()
        ], $children ? $children[$parentId] : []));
    }

    public function updateCategory(int $id)
    {
        $putParams = $this->getPutParams();
        $title = Filter::get($putParams['title']);
        $category = Categories::getById($id);
        $category->update([
            'title' => $title,
            'url' => Filter::get($putParams['url']),
            'emoji' => Filter::get($putParams['emoji'])
        ]);
        Synonyms::updateCategory($category, Filter::getSynonyms($putParams['synonyms']));
        Flash::add(json_encode([
            'message' => 'Категория <a href="/karman/categories/'
                . $id
                . '">"'
                . $title
                . '"</a> обновлена.',
            'type' => 'success'
        ]));
        $this->showJsonResponse(['success' => true]);
    }

    public function removeEmoji(int $id)
    {
        $category = Categories::getById($id);
        $category->update([
            'emoji' => ''
        ]);
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