<?php

$flashMessage = \Palto\Flash::get();
/**
 * @var $this \Palto\Layout
 */
$categoryWithChildrenIds = $this->getCategory() ? $this->getCategory()->getWithChildrenIds() : [];
$ads = $this->getAds();
$pager = new \Palto\Pager($this->getDispatcher());
$categoriesTitle = $this->getCategory() ? implode(' - ', $this->getCategory()->getWithParentsTitles()) : '';

$this->partial('header.inc', [
    'title' => ($categoriesTitle ? $categoriesTitle . ' - ' : $categoriesTitle)
        . 'Ogłoszenia w '
        . $this->getRegion()->getTitle(),
    'description' => 'Agregator wszystkich tablic ogłoszeniowych w '
        . ($this->getCategory() ? implode(' - ', $this->getCategory()->getWithParentsTitles([$this->getRegion()->getTitle()])) : $this->getRegion()->getTitle()),
    'nextPageUrl' => $pager->getNextPageUrl(),
    'previousPageUrl' => $pager->getPreviousPageUrl(),
]);
?>
    <div class="bread"
         itemscope
         itemtype="http://schema.org/BreadcrumbList"
    >
        <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getBreadcrumbUrls()]);?>
    </div>
    <h1><?php if ($this->getCategory()) :?>
            <?= $this->getCategory()->getTitle()?> w
        <?php endif;?>
        <?= $this->getRegion()->getTitle() == 'Polska'
            ? 'Polske'
            : $this->getRegion()->getTitle()
        ?>: Ogłoszenia drobne z OLX
    </h1>
<?php if ($flashMessage) :?>
    <div class="alert"><?=$flashMessage?></div>
<?php endif;?>

<?php $categories = $this->getCategory()
    ? $this->getWithAdsCategories($this->getCategory()->getId())
    : $this->getWithAdsCategories()
?>
<?php if ($categories) :?>
    <ul class="sub_cat">

        <?php foreach ($categories as $childCategory) :?>
            <li><a href="<?=$this->generateCategoryUrl($childCategory)?>"><?=$childCategory->getTitle()?></a></li>
        <?php endforeach?>
    </ul>
<?php endif;?>

    <div class="region_cat"><b>Region:</b> <?= $this->getRegion()->getTitle() ?></div>
    <table class="serp">
        <?php foreach ($ads as $adIndex => $ad) :?>
            <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>

            <?php if (in_array($adIndex + 1, [5, 15])) : ?>
                <!--                                Counter-->
            <?php elseif (in_array($adIndex + 1, [2, 10, 21])) : ?>
                <!--                                Counter-->
            <?php endif; ?>
        <?php endforeach;?>
    </table>
<?php $this->partial('pager.inc', [
    'pageNumber' => $pager->getPageNumber(),
    'nextPageUrl' => $pager->getNextPageUrl(),
    'previousPageUrl' => $pager->getPreviousPageUrl(),
])?>

<?php $this->partial('footer.inc');