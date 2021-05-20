<?php

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
    'title' => 'Title: ' . implode(
        ' - ',
        array_filter(array_merge(
            array_column($this->getCurrentCategory()['parents'], 'title'),
            [$this->getCurrentCategory()['title']],
            [$this->getCurrentRegion()['title']]
        ))
    ),
    'description' => 'Category Description: '
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
<div id="col-mid">
    <div class="col-mid-inner">
        <div class="cmi-1">
            <div class="cmi-1-1">
                <div class="block bread">
                    <div class="bl1">
                        <div class="b1" itemscope itemtype="http://schema.org/BreadcrumbList">
                            <?php foreach ($this->getListBreadcrumbUrls() as $breadcrumbKey => $breadcrumbItem) :?>
                                <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                    <a itemprop="item" href="<?=$breadcrumbItem['url']?>" class="bread">
                                        <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
                                    </a>
                                    <meta itemprop="position" content="<?=$breadcrumbKey + 1?>"/>
                                </span>
                                <?php if ($this->getCurrentCategory()['title']) :?>
                                    <span class="sep">»</span>
                                <?php endif;?>
                            <?php endforeach;?>

                            <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <span itemprop="name"><?= $this->getCurrentCategory()['title'] ?></span>
                                <meta itemprop="position" content="<?=$breadcrumbKey ?? 0 + 1?>"/>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="main-ob">
                    <div class="block">
                        <div class="bl1">
                            <h1>
                                <?php if ($this->getCurrentCategory()['title']) :?>
                                    <?= $this->getCurrentCategory()['title']?> in
                                <?php endif;?>

                                <?= $this->getCurrentRegion()['title'] ?>
                            </h1>
                            <ul class="adap">
                                <?php $categories = $this->getCurrentCategory()['id']
                                    ? $this->getCategories($this->getCurrentCategory()['id'])
                                    : $this->getCategories(0, 1)
                                ?>
                                <?php foreach ($categories as $childCategory) :?>
                                    <li><a href="<?=$this->generateCategoryUrl($childCategory)?>">
                                            <?=$childCategory['title']?>
                                        </a>
                                    </li>
                                <?php endforeach?>
                            </ul>
                            <div class="filter-level">
                                <div class="fl1">
                                    <div class="reg-sel">
                                        Region: <a class="ch-region bold">
                                            <?= $this->getCurrentRegion()['title'] ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block">
                        <div class="bl1">
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
                                <hr>
                            <?php endforeach;?>

                            <?php $this->partial('pagination.inc', ['paginationUrls' => $this->getPaginationUrls()])?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->partial('footer.inc');