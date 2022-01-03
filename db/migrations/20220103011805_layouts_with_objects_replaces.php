<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LayoutsWithObjectsReplaces extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        \Palto\Backup::createArchive();
        $replaces = [
            'layouts/list.php' => [
                '$flashMessage = $this->getFlashMessage();' => '$flashMessage = \Palto\Flash::get();',
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$categoryWithChildrenIds = ...' => '$categoryWithChildrenIds = $this->getCategory() ? $this->getCategory()->getWithChildrenIds() : [];',
                '$ads = $this->getAds(...' => '$ads = $this->getAds();',
                '$this->initPager($this->hasNextPage(count($ads)))' => '$pager = new \Palto\Pager($this->getDispatcher());',
                '$categoriesTitle = implode(\' - \', $this->getCurrentCategory()[\'titles\']);' => '$categoriesTitle = $this->getCategory() ? implode(\' - \', $this->getCategory()->getWithParentsTitles()); : ""',
                '$this->getCurrentRegion()[\'title\']' => '$this->getRegion()->getTitle()',
                'array_filter(array_merge(
                array_column($this->getCurrentCategory()[\'parents\'], \'title\'),
                [$this->getCategory()->getTitle()],
                [$this->getRegion()->getTitle()]
            ))' => '($this->getCategory() ? $this->getCategory()->getWithParentsTitles([$this->getRegion()->getTitle()]) : $this->getRegion()->getTitle())',
                '$this->getNextPageUrl()' => '$pager->getNextPageUrl()',
                '$this->getPreviousPageUrl()' => '$pager->getPreviousPageUrl()',
                '$this->getListBreadcrumbUrls()' => '$this->getBreadcrumbUrls()',
                '$this->getCurrentCategory()[\'title\']' => '$this->getCategory()',
                '$this->getCurrentCategory()[\'id\']' => '$this->getCategory()',
                '$this->getPageNumber()' => '$pager->getPageNumber()',
                '$childCategory[\'title\']' => '$childCategory->getTitle()'
            ],
            'layouts/index.php' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '[\'icon_url\']' => '->getIconUrl()',
                '[\'icon_text\']' => '->getIconText()',
                '[\'title\']' => '->getTitle()',
                '[\'id\']' => '->getId()',
                '$this->getFlashMessage()' => '\Palto\Flash::get()',
                '$this->getCurrentCategory()[\'id\']' => '$this->getCategory()->getId()',
                '= $this->getAds(...' => '= $this->getAds();',
                '$categoryWithChildrenIds = ...' => '$categoryWithChildrenIds = $this->getCategory() ? $this->getCategory()->getWithChildrenIds() : [];',
                'array_column($this->getCurrentCategory()[\'children\'], \'id\')' => '$this->getCategory()->getChildrenIds()',
                '$this->getCurrentCategory()[\'children\']' => '$this->getCategory()->getChildren()',
                '$this->initPager($this->hasNextPage(count($ads)))' => '$pager = new \Palto\Pager($this->getDispatcher())',
                '$categoriesTitle = implode(\' - \', $this->getCurrentCategory()[\'titles\']); ' => '$categoriesTitle = $this->getCategory() ? implode(\' - \', $this->getCategory()->getWithParentsTitles()) : \'\';',
                '. implode(
        \' - \',
        array_filter(array_merge(
            array_column($this->getCurrentCategory()[\'parents\'], \'title\'),
            [$this->getCurrentCategory()[\'title\']],
            [$this->getCurrentRegion()[\'title\']]
        ))
    ) ' => '($this->getCategory() ? implode(\' - \', $this->getCategory()->getWithParentsTitles([$this->getRegion()->getTitle()])) : $this->getRegion()->getTitle())',
                '$this->getNextPageUrl() ' => '$pager->getNextPageUrl()',
                '$this->getPreviousPageUrl()' => '$pager->getPreviousPageUrl()',
                '$this->getCurrentCategory()[\'title\']' => '$this->getCategory()->getTitle()',
                '?php if ($this->getCategory()->getTitle())' => '?php if ($this->getCategory())',
                '$this->getCurrentRegion()[\'title\']' => '$this->getRegion()->getTitle()',
                '$categories = $this->getCurrentCategory()[\'id\']
    ? $this->getWithAdsCategories($this->getCurrentCategory()[\'id\'])
    : $this->getWithAdsCategories(0, 1)' => '$categories = $this->getCategory()
        ? $this->getWithAdsCategories($this->getCategory()->getId())
        : $this->getWithAdsCategories())',
                '$this->getPageNumber()' => '$pager->getPageNumber()',
                '$this->getNextPageUrl()' => '$pager->getNextPageUrl()',
                '$this->getWithAdsCategories(0, 1)' => '$this->getWithAdsCategories()',
                'getWithAdsCategories($level1Category->getId())' => 'getWithAdsCategories($level1Category)',
                '$this->getWithAdsCategories($this->getCategory()->getId())' => '$this->getWithAdsCategories($this->getCategory())',
            ],
            'layouts/partials/ad_in_list.inc' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$ad[\'images\']' => '$ad->getImages()',
                '$ad[\'title\']' => '$ad->getTitle()',
                '$this->generateShortText($ad[\'text\'])' => '\Palto\Filter::shortText($ad->getText())',
                '$this->getListAdBreadcrumbUrls($ad)' => '$this->getBreadcrumbUrls()',
                '$this->generateRegionUrl($ad[\'region\'])' => '$this->generateRegionUrl($ad->getRegion())',
                '$ad[\'region\'][\'title\']' => '$ad->getRegion()->getTitle()',
                '$ad[\'price\']' => '$ad->getPrice()',
                '$ad[\'currency\']' => '$ad->getCurrency()'
            ],
            'layouts/partials/pager.inc' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$this->previousPageUrl' => '$this->getPartialVariable(\'previousPageUrl\')',
                '$this->nextPageUrl' => '$this->getPartialVariable(\'nextPageUrl\')',
            ],
            'layouts/partials/header.inc' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$this->getWithAdsCategories(5)' => '$this->getWithAdsCategories(0, 1, 5)',
                '$popularLevel1Category[\'title\']' => '$popularLevel1Category->getTitle()',
                'getWithAdsCategories(0, 1, 5)' => 'getWithAdsCategories(null, 5)'
            ],
            'layouts/regions-list.php' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$this->getRegions(0, 1)' => '$this->getWithAdsRegions(0, intval($this->getParameter(\'limit\')))',
                '$level1Region[\'title\']' => '$level1Region->getTitle()',
                '$this->getRegions($level1Region[\'id\'])' => '$this->getWithAdsRegions($level1Region->getId(), intval($this->getParameter(\'limit\')))',
                '$level2Region[\'title\']' => '$level2Region->getTitle()',
                '$this->getWithAdsRegions($level1Region->getId())' => '$this->getWithAdsRegions($level1Region)'
            ],
            'layouts/categories-list.php' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$this->getWithAdsCategories(0, 1)' => '$this->getWithAdsCategories()',
                '$level1Category[\'title\']' => '$level1Category->getTitle()',
                '$level1Category[\'id\']' => '$level1Category->getId()',
                '$level2Category[\'title\']' => '$level2Category->getTitle()',
                '$level2Category[\'id\']' => '$level2Category->getId()',
                '$level3Category[\'title\']' => '$level3Category->getTitle()',
                '$this->getWithAdsCategories($level1Category->getId())' => '$this->getWithAdsCategories($level1Category)'
            ],
            'layouts/ad.php' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$this->getCurrentAd()[\'title\']' => '$this->getAd()->getTitle()',
                '$this->getCurrentCategory()[\'titles\']' => '$this->getAd()->getCategory()->getWithParentsTitles()',
                '$this->getCurrentAd()[\'address\']' => '$this->getAd()->getAddress()',
                '$this->getCurrentRegion()[\'title\']' => '$this->getRegion()->getTitle()',
                '$this->generateShortText($this->getCurrentAd()[\'text\'])' => '\Palto\Filter::shortText($this->getAd()->getText())',
                '\'nextPageUrl\' => $this->getNextPageUrl(),' => '',
                '\'previousPageUrl\' => $this->getPreviousPageUrl(),' => '',
                '$this->getCurrentAd()[\'coordinates\']' => '$this->getAd()->getCoordinates()',
                '$this->getAdBreadcrumbUrls()' => '$this->getBreadcrumbUrls()',
                '$this->getCurrentAd()[\'images\']' => '$this->getAd()->getImages()',
                '$this->getDomainUrl()' => '\Palto\Config::getDomainUrl()',
                '$this->getCurrentAd()[\'details\']' => '$this->getAd()->getDetails()',
                '$this->getCurrentAd()[\'text\']' => '$this->getAd()->getText()',
                '$this->getCurrentAd()[\'price\']' => '$this->getAd()->getPrice()',
                '$this->getCurrentAd()[\'currency\']' => '$this->getAd()->getCurrency()',
                '$this->getCurrentAd()[\'seller_postfix\'] ?? \'\'' => '$this->getAd()->getSellerPostfix()',
                '$this->getLatitude()' => '$this->getAd()->getLatitude()',
                '$this->getLongitute()' => '$this->getAd()->getLongitute()',
                '$this->getCurrentAd()[\'region\'][\'title\']' => '$this->getAd()->getRegion()->getTitle()',
                '$this->getCurrentAd()[\'region\']' => '$this->getAd()->getRegion()',
                '$this->getCurrentAd()[\'seller_name\']' => '$this->getAd()->getSellerName()',
                '$this->getCurrentAd()[\'seller_phone\']' => '$this->getAd()->getSellerPhone()',
                '$this->getCurrentAd()[\'url\']' => '$this->getAd()->getUrl()',
                '(new DateTime($this->getCurrentAd()[\'create_time\']))' => '$this->getAd()->getCreateTime()',
                '$this->getCurrentAd()[\'id\']' => '$this->getAd()->getId()',
                '$this->getAds($this->getCurrentCategory()[\'id\'], $this->getCurrentRegion()[\'id\'], 6)' => '$this->getSimilarAds()',
                '$similarAd[\'id\']' => '$similarAd->getId()',
                '(new DateTime($this->getCurrentAd()[\'post_time\']))' => '$this->getAd()->getPostTime()'
            ],
            'layouts/404.php' => [
                '$this \Palto\Palto' => '$this \Palto\Layout',
                '$this->getCurrentAd()' => '$this->getAd()',
                'getWithAdsCategories(0, 1)' => 'getWithAdsCategories()',
                '$level1Category[\'icon_url\']' => '$level1Category->getIconUrl()',
                '$level1Category[\'icon_text\']' => '$level1Category->getIconText()',
                '$level1Category[\'title\']' => '$level1Category->getTitle()',
            ],
            'layouts/registration.php' => [
                '$this \Palto\Palto' => '$this \Palto\Layout'
            ]
        ];
        $rootDirectory = realpath('.');
        while (!file_exists($rootDirectory . '/.env')) {
            $rootDirectory = realpath('..');
        }

        \Palto\Directory::setRootDirectory($rootDirectory);
        \Palto\Update::replaceCode($replaces);
        \Palto\Update::check();
    }
}
