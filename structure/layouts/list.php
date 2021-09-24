<?php

$flashMessage = $this->getFlashMessage();
/**
 * @var $this \Palto\Palto
 */
$categoryWithChildrenIds = $this->getCurrentCategory()['id']
    ? array_merge(
        [$this->getCurrentCategory()['id']],
        array_column($this->getCurrentCategory()['children'], 'id')
    ) : [];
$ads = $this->getAds(
    $categoryWithChildrenIds,
    $this->getCurrentRegion()['id'],
    $this->getAdsLimit(),
    $this->getAdsOffset()
);
$this->initPager($this->hasNextPage(count($ads)));
$categoriesTitle = implode(' - ', $this->getCurrentCategory()['titles']);
$this->partial('header.inc', [
    'title' => ($categoriesTitle ? $categoriesTitle . ' - ' : $categoriesTitle)
        . 'Ogłoszenia w '
        . $this->getCurrentRegion()['title'],
    'description' => 'Agregator wszystkich tablic ogłoszeniowych w '
        . implode(
            ' - ',
            array_filter(array_merge(
                array_column($this->getCurrentCategory()['parents'], 'title'),
                [$this->getCurrentCategory()['title']],
                [$this->getCurrentRegion()['title']]
            ))
        ),
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
]);
?>
    <div class="bread"
         itemscope
         itemtype="http://schema.org/BreadcrumbList"
    >
        <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getListBreadcrumbUrls()]);?>
    </div>
    <h1><?php if ($this->getCurrentCategory()['title']) :?>
            <?= $this->getCurrentCategory()['title']?> w
        <?php endif;?>
        <?= $this->getCurrentRegion()['title'] == 'Polska'
            ? 'Polske'
            : $this->getCurrentRegion()['title']
        ?>: Ogłoszenia drobne z OLX
    </h1>
<?php if ($flashMessage) :?>
    <div class="alert"><?=$flashMessage?></div>
<?php endif;?>

<?php $categories = $this->getCurrentCategory()['id']
    ? $this->getWithAdsCategories($this->getCurrentCategory()['id'])
    : $this->getWithAdsCategories(0, 1)
?>
<?php if ($categories) :?>
    <ul class="sub_cat">

        <?php foreach ($categories as $childCategory) :?>
            <li><a href="<?=$this->generateCategoryUrl($childCategory)?>"><?=$childCategory['title']?></a></li>
        <?php endforeach?>
    </ul>
<?php endif;?>

    <div class="region_cat"><b>Region:</b> <?= $this->getCurrentRegion()['title'] ?></div>
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
    'pageNumber' => $this->getPageNumber(),
    'nextPageUrl' => $this->getNextPageUrl(),
    'previousPageUrl' => $this->getPreviousPageUrl(),
])?>

<?php $this->partial('footer.inc');