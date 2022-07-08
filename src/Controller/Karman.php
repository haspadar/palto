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
use Palto\Logger;
use Palto\Logs;
use Palto\Pages;
use Palto\Plural;
use Palto\Regions;
use Palto\Synonym;
use Palto\Synonyms;
use Palto\Settings;
use Palto\Templates;
use Palto\Translate;
use Palto\Translates;
use Palto\Url;
use Palto\Validator;

class Karman
{
    protected Engine $templatesEngine;
    protected Url $url;
    const LIMIT = 100;

    public function __construct()
    {
        Auth::check();
        $this->templatesEngine = new Engine(Directory::getKarmanTemplatesDirectory());
        $this->templatesEngine->loadExtension(new Asset(Directory::getPublicDirectory(), false));
        $this->url = new Url();
        $this->templatesEngine->addData([
            'flash' => Flash::receive(),
            'url' => $this->url,
            'actual_complaints_count' => \Palto\Complaints::getActualComplaintsCount()
        ]);
    }

    public function showStatus()
    {
        $this->templatesEngine->addData([
            'title' => 'Приборы'
        ]);
        echo $this->templatesEngine->make('status');
    }

    public function showSettings()
    {
        $this->templatesEngine->addData([
            'title' => 'Настройки',
            'settings' => Settings::getValues()
        ]);
        echo $this->templatesEngine->make('settings');
    }

    public function updateSetting(int $id)
    {
        $putParams = $this->getPutParams();
        $setting = Settings::getById($id);
        $setting['value'] = Filter::get($putParams['value']);
        Settings::update($setting, $id);
        Flash::add(json_encode([
            'message' => 'Настройка <a href="/karman/settings/'
                . $setting['id']
                . '">"'
                . $setting['name']
                . '"</a> обновлена.',
            'type' => 'success'
        ]));
        $this->showJsonResponse(['success' => true]);
    }

    public function updateTranslate(int $id)
    {
        $putParams = $this->getPutParams();
        Translates::update(['value' => Filter::get($putParams['value'])], $id);
        Flash::add(json_encode([
            'message' => 'Перевод <a href="/karman/translates/'
                . $id
                . '">"'
                . (Translates::getById($id))->getName()
                . '"</a> обновлен.',
            'type' => 'success'
        ]));
        $this->showJsonResponse(['success' => true]);
    }

    public function showSetting(int $id)
    {
        $setting = Settings::getById($id);
        $this->templatesEngine->addData([
            'title' => 'Настройка "' . $setting['name'] . '"' ,
            'setting' => $setting,
            'themes' => Templates::getThemes(),
        ]);
        echo $this->templatesEngine->make('setting');
    }

    public function showTranslate(int $id)
    {
        $translate = Translates::getById($id);
        $this->templatesEngine->addData([
            'title' => 'Перевод "' . $translate->getName() . '"' ,
            'translate' => $translate,
        ]);
        echo $this->templatesEngine->make('translate');
    }

    public function showPages()
    {
        $this->templatesEngine->addData([
            'title' => 'Страницы',
            'pages' => Pages::getPages(0, 'priority ASC')
        ]);
        echo $this->templatesEngine->make('pages');
    }

    public function showPage(int $id)
    {
        $page = Pages::getById($id);
        $this->templatesEngine->addData([
            'title' => 'Страница "' . $page->getName() . '"' ,
            'page' => $page,
            'templates' => Templates::getTemplates(),
            'functions' => Templates::getFunctions()
        ]);
        echo $this->templatesEngine->make('page');
    }

    public function updatePage(int $id)
    {
        $putParams = $this->getPutParams();
        $page = Pages::getById($id);
        $updates = Filter::getArray($putParams);
        $updates['is_enabled'] = intval($updates['is_enabled'] ?? 0);
        Pages::update($updates, $id);
        Flash::add(json_encode([
            'message' => 'Страница <a href="/karman/pages/'
                . $page->getId()
                . '">"'
                . $page->getName()
                . '"</a> обновлена.',
            'type' => 'success'
        ]));
        $this->showJsonResponse(['success' => true]);
    }

    public function showTemplates()
    {
        $this->templatesEngine->addData([
            'title' => 'Шаблоны',
            'templates' => Templates::getTemplates(),
            'pages' => Pages::getPages(0, 'priority ASC')
        ]);
        echo $this->templatesEngine->make('templates');
    }

    public function showTranslates()
    {
        $this->templatesEngine->addData([
            'title' => 'Переводы',
            'translates' => Translates::getTranslates(),
        ]);
        echo $this->templatesEngine->make('translates');
    }

    public function showKarmanIndex()
    {
        $this->redirect('/karman/complaints?cache=0');
    }

    public function showComplaints()
    {
        $this->templatesEngine->addData([
            'complaints' => \Palto\Complaints::getActualComplaints(),
            'title' => 'Жалобы',
        ]);
        echo $this->templatesEngine->make('complaints');
    }

    public function showComplaint(int $id)
    {
        $complaint = \Palto\Complaints::getComplaint($id);
        $this->templatesEngine->addData([
            'complaint' => $complaint,
            'ad' => Ads::getById($complaint['ad_id']),
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
                $complaint = \Palto\Complaints::getComplaint($id);
                $ad = Ads::getById($complaint['ad_id']);
                \Palto\Complaints::removeAd($id);
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

    public function showInfoLogsDirectories()
    {
        $this->templatesEngine->addData([
            'title' => 'Все логи',
            'type' => 'info',
            'directories' => Directory::getLogsDirectories(),
        ]);
        echo $this->templatesEngine->make('logs-directories');
    }

    public function showErrorLogsDirectories()
    {
        $this->templatesEngine->addData([
            'title' => 'Все ошибки',
            'type' => 'error',
            'directories' => Directory::getLogsDirectories(),
        ]);
        echo $this->templatesEngine->make('logs-directories');
    }

    public function showInfoLogs(string $name)
    {
        $this->templatesEngine->addData([
            'title' => 'Логи "' . $name . '"',
            'type' => 'info',
            'directory' => $name,
            'breadcrumbs' => array_merge([[
                'title' => 'Все логи',
                'url' => '/karman/info-logs-directories?cache=0'
            ], [
                'title' => 'Логи "' . $name . '"',
            ]])
        ]);
        echo $this->templatesEngine->make('logs');
    }

    public function getLogs(string $name, string $type)
    {
        $this->showJsonResponse(['logs' => array_reverse(Logs::getLogs($name, $type))]);
    }

    public function showErrorLogs(string $name)
    {
        $this->templatesEngine->addData([
            'title' => 'Ошибки "' . $name . '"',
            'type' => 'error',
            'directory' => $name,
            'breadcrumbs' => array_merge([[
                'title' => 'Все ошибки',
                'url' => '/karman/error-logs-directories?cache=0'
            ], [
                'title' => 'Ошибки "' . $name . '"',
            ]])
        ]);
        echo $this->templatesEngine->make('logs');
    }

    public function findAdCategory(int $id)
    {
        $ad = Ads::getById($id);
        $found = \Palto\Synonyms::find($ad, \Palto\Synonyms::getAll());
        if ($found['synonym']) {
            $response = 'Нашлась категория <span class="fw-bolder">"'
                . $found['category']->getPath()
                . '"</span>'
                . ' по синониму "'
                . $found['synonym']->getTitle()
                . '" в поле "'
                . $found['field']
                . '"';
        } else {
            $response = 'Категория не нашлась (<span class="fw-bolder">"' . $found['category']->getPath() . '"</span>)';
        }

        $this->showJsonResponse(['report' => $response]);
    }

    public function showKarmanAd(int $id)
    {
        $ad = Ads::getById($id);
        $this->templatesEngine->addData([
            'title' => 'Объявление ' . $id,
            'ad' => $ad,
            'breadcrumbs' => array_merge([[
                'title' => 'Объявления',
                'url' => '/karman/ads?cache=0'
            ], [
                'title' => 'Объявление ' . $ad->getId()
            ]])
        ]);
        echo $this->templatesEngine->make('ad');
    }

    public function showAds($page)
    {
        $page = Filter::getPageNumber($page);
        $offset = ($page - 1) * self::LIMIT;
        $adsCount = Ads::getAdsCount();
        $this->templatesEngine->addData([
            'title' => 'Объявления',
            'ads' => Ads::getAds(null, null, self::LIMIT, $offset),
            'page' => $page,
            'pages_count' => ceil($adsCount / self::LIMIT),
        ]);
        echo $this->templatesEngine->make('ads');
    }

    public function showCategoryAds(int $id, $page)
    {
        $category = Categories::getById($id);
        $page = Filter::getPageNumber($page);
        $offset = ($page - 1) * self::LIMIT;
        $adsCount = Ads::getAdsCount([$category->getId()]);
        $this->templatesEngine->addData([
            'title' => 'Объявления ' . $category->getTitle(),
            'category' => $category,
            'ads' => Ads::getAds(null, $category, self::LIMIT, $offset),
            'page' => $page,
            'pages_count' => ceil($adsCount / self::LIMIT),
            'breadcrumbs' => array_merge([[
                'title' => 'Категории',
                'url' => '/karman/categories?cache=0'
            ], [
                'title' => $category->getTitle(),
                'url' => '/karman/categories/' . $category->getId() . '?cache=0'
            ]], [[
                'title' => 'Объявления',
            ]])
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
        $categories = Categories::getChildren(
            [$category->getId()],
            0,
            0,
            'title'
        )[$category->getId()] ?? [];
        $this->templatesEngine->addData([
            'title' => 'Категория',
            'category' => $category,
            'categories' => $categories,
            'ads_counts' => Ads::getCategoriesAdsCounts(
                array_map(fn(Category $category) => $category->getId(), $categories),
                $category->getLevel() + 1
            ) + Ads::getCategoriesAdsCounts(
                    array_map(fn(Category $category) => $category->getId(), [$category]),
                    $category->getLevel()
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
        $undefinedCategories = Categories::getUndefinedAll('level ASC');
        $this->templatesEngine->addData([
            'title' => 'Категории',
            'categories' => $categories,
            'ads_counts' =>
                Ads::getCategoriesAdsCounts(array_map(fn(Category $category) => $category->getId(), $categories), 1)
                + Ads::getCategoriesAdsCounts(array_map(fn(Category $category) => $category->getId(), $undefinedCategories)),
            'undefined_categories' => $undefinedCategories,
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
        $children = $parentId ? Categories::getChildren([$parentId], 0, 0, 'title') : [];
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

    public function removeCategory(int $id)
    {
        $category = Categories::getById($id);
        if ($category) {
            $category->remove();
            Flash::add(json_encode([
                'message' => 'Категория "' . $category->getTitle() . '" удалена.',
                'type' => 'success'
            ]));
            $this->showJsonResponse(['success' => true]);
        } else {
            $this->showJsonResponse(['error' => 'Категория с id="' . $id . '" не найдена']);
        }
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

    private function redirect(string $url)
    {
        header('Location: ' . $url, true, 301);
    }
}