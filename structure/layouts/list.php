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
$count = $this->getAdsCount($categoryWithChildrenIds, $this->getCurrentRegion()['id']);
$this->initPagination($count);
$this->partial('header.inc', [
    'title' => implode(
        ' - ',
        array_filter(array_merge(
            [$this->getCurrentCategory()['title']],
            array_column($this->getCurrentCategory()['parents'], 'title'),
            [$this->getCurrentRegion()['title']]
        ))
    ) . ' Classifieds',
    'description' => 'Aggregator of all classifieds boards in '
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
<div class="bread" itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php $this->partial('breadcrumb.inc', ['breadcrumbUrls' => $this->getListBreadcrumbUrls()]);?>
</div>
<h1><?php if ($this->getCurrentCategory()['title']) :?><?= $this->getCurrentCategory()['title']?> in <?php endif;?><?= $this->getCurrentRegion()['title'] ?>: Classified Ads from craigslist</h1>
<?php if ($flashMessage) :?>
    <div class="alert"><?=$flashMessage?></div>
<?php endif;?>

<?php $categories = $this->getCurrentCategory()['id']
    ? $this->getCategories($this->getCurrentCategory()['id'])
    : $this->getCategories(0, 1)
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
    <?php
    foreach ($this->getAds(
            $categoryWithChildrenIds,
            $this->getCurrentRegion()['id'],
            $this->getAdsLimit(),
            $this->getAdsOffset()
        ) as $adIndex => $ad
    ) :?>
        <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>

        <?php if (in_array($adIndex + 1, [5, 15])) : ?>
            <!--                                Counter-->
        <?php elseif (in_array($adIndex + 1, [2, 10, 21])) : ?>
            <!--                                Counter-->
        <?php endif; ?>
    <?php endforeach;?>
</table>
    <?php $this->partial('pagination.inc', ['paginationUrls' => $this->getPaginationUrls()])?>

<?php $this->partial('footer.inc');