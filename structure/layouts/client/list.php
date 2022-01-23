<?php

$flashMessage = \Palto\Flash::get();
/**
 * @var $this \Palto\Layout\Client
 */
$categoryWithChildrenIds = $this->getCategory() ? $this->getCategory()->getWithChildrenIds() : [];
$ads = $this->getAds();
$pager = new \Palto\Pager($this->getDispatcher());
$this->partial('header.inc', [
    'title' => $this->translate('list_title'),
    'description' => $this->translate('list_description'),
    'nextPageUrl' => $pager->getNextPageUrl(),
    'previousPageUrl' => $pager->getPreviousPageUrl(),
]);
?>
    <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getBreadcrumbUrls()]);?>
    <h1><?=$this->translate('list_h1')?></h1>
    <?=\Palto\Counters::get('google')?>
<?php if ($flashMessage) :?>
    <div class="alert"><?=$flashMessage?></div>
<?php endif;?>

<?php $categories = $this->getCategory()
    ? $this->getWithAdsCategories($this->getCategory())
    : $this->getWithAdsCategories()
?>
<?php if ($categories) :?>
    <ul class="sub_cat">

        <?php foreach ($categories as $childCategory) :?>
            <li><a href="<?=$this->generateCategoryUrl($childCategory)?>"><?=$childCategory->getTitle()?></a></li>
        <?php endforeach?>
    </ul>
<?php endif;?>

    <div class="region_cat"><b><?=$this->translate('Регион')?>:</b> <?= $this->getRegion()->getTitle() ?></div>
    <table class="serp">
        <?php foreach ($ads as $adIndex => $ad) :?>
            <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>
            <?php if (in_array($adIndex + 1, [5, 15])) : ?>
                <?=\Palto\Counters::get('google')?>
            <?php elseif (in_array($adIndex + 1, [2, 10, 21])) : ?>
                <?=\Palto\Counters::get('google')?>
            <?php endif; ?>
        <?php endforeach;?>
    </table>
<?php $this->partial('pager.inc', [
    'pageNumber' => $pager->getPageNumber(),
    'nextPageUrl' => $pager->getNextPageUrl(),
    'previousPageUrl' => $pager->getPreviousPageUrl(),
])?>

<?php $this->partial('footer.inc');