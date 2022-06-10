<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<div class="blocks">
    <?php if ($this->data['h1']) :?>
        <div class="blocks__headline headline">
            <h1><?= $this->data['h1'] ?></h1>
        </div>
    <?php endif;?>

</div>

<div class="regions">
    <?php /** @var $level1Region \Palto\Region*/?>
    <?php if ($this->data['regions']) :?>
        <?php foreach ($this->data['regions'] as $region) :?>
            <div class="region">
                <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . '/img/icon-block.png') ?>" alt="block">
                <a href="<?=$region->generateUrl()?>">
                    <?=$region->getTitle()?>
                </a>
            </div>
        <?php endforeach;?>

        <div class="region">
            <a href="/regions">
                <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . '/img/icon-block.png') ?>" alt="block">
                View All
            </a>
        </div>
    <?php endif;?>
</div>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>

<div class="categories">
    <div class="categories__headline headline">
        <h2><?=$this->translate('Категории')?></h2>
    </div>
    <div class="categories__content">
        <?php /** @var $level1Category \Palto\Category */?>
        <?php foreach (\Palto\Categories::getLiveCategoriesWithChildren(
            \Palto\Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_1'),
            \Palto\Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2')
        ) as $level1Category) :?>
            <?php /** @var $level2Category \Palto\Category */?>
            <?php $level2Categories = $level1Category->getLiveChildren($this->data['region'], \Palto\Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2'));?>
            <?php if ($level2Categories) :?>
                <ul class="categories__list">
                    <span class="categories__headline-link">
                        <?php if ($level1Category->getEmoji()) :?>
                            <?=$level1Category->getEmoji()?>
                        <?php elseif ($level1Category->getIconUrl()) :?>
                            <img src="<?=$level1Category->getIconUrl()?>"
                                 title="<?=$level1Category->getIconText()?>"
                                 class="icm"
                                 alt="list"
                                 onerror="this.src='/img/no-photo.png'"
                            />
                        <?php endif?>
                        <a href="<?=$level1Category->generateUrl($this->data['region'])?>"><?=$level1Category->getTitle()?></a>
                    </span>
                    <?php foreach ($level2Categories as  $level2Category) :?>
                        <li class="categories__link">
                            <a href="<?=$level2Category->generateUrl($this->data['region'])?>">
                                <?=$level2Category->getTitle()?>
                            </a>
                        </li>
                    <?php endforeach;?>

                    <li class="categories__link">
                        <a href="<?=$level1Category->generateUrl($this->data['region'])?>">
                            Other...
                        </a>
                    </li>
                </ul>
            <?php endif;?>
        <?php endforeach;?>
    </div>
</div>
<div class="hot-obs">
    <div class="hot-obs__content">
        <div class="hot-obs__headline headline">
            <h2><?=$this->translate('Горячие объявления')?></h2>
        </div>
        <div class="hot-obs__items">
            <?php foreach (\Palto\Ads::getHotAds($this->data['region'], 5) as $ad) :?>
                <?php $this->insert('partials/ad_in_list', ['ad' => $ad])?>
            <?php endforeach;?>
        </div>
    </div>
</div>
<div class="new-obs">
    <div class="new-obs__content">
        <div class="hot-obs__headline headline">
            <h2><?=$this->translate('Новые объявления')?></h2>
        </div>
        <div class="new-obs__items">
            <?php foreach (\Palto\Ads::getAds($this->data['region'], $this->data['category'], \Palto\Config::get('HOT_LAYOUT_NEW_ADS')) as $ad) :?>
                <?php $this->insert('partials/ad_in_list', ['ad' => $ad])?>
            <?php endforeach;?>

        </div>
    </div>
</div>