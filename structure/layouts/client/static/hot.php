<?php

/**
 * @var $this Client
 */

use Palto\Category;
use Palto\Config;
use Palto\Layout\Client;
use Palto\Regions;

$this->partial('header.inc', [
    'title' => $this->translate('index_title'),
    'description' => $this->translate('index_description'),
]);
?>
<h1><?=$this->translate('index_h1')?></h1>
<?php foreach (Regions::getWithAdsRegions(null, Config::get('HOT_LAYOUT_REGIONS')) as $region) :?>
    <div class="span-d regions">📍<a href="<?=$region->generateUrl()?>"><strong> <?=$region->getTitle()?></strong></a></div>
<?php endforeach;?>
<?=\Palto\Counters::get('google')?>
<br style="clear: both">
<br style="clear: both">

<h2>🗂 <?=$this->translate('Категории')?></h2>
<?php $level1Categories = array_filter(
        $this->getWithAdsCategories(null, Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_1')),
        fn(Category $category) => count($this->getWithAdsCategories($category, Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2'))) == Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2')
);
foreach ($level1Categories as $level1Category) :?>
    <div class="span-d">
        <p><a href="<?=$this->generateCategoryUrl($level1Category)?>">
                <?php if ($level1Category->getEmoji()) :?>
                    <?=$level1Category->getEmoji()?>
                <?php elseif ($level1Category->getIconUrl()) :?>
                    <img src="<?=$level1Category->getIconUrl()?>"
                         title="<?=$level1Category->getIconText()?>"
                         class="icm" />
                <?php endif?>

                <strong> <?=$level1Category->getTitle()?></strong>
            </a>
        </p>
        <?php if ($level2Categories = $this->getWithAdsCategories($level1Category, Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2'))) :?>
            <ul>
                <?php foreach ($level2Categories as  $level2Category) :?>
                    <li><a href="<?=$this->generateCategoryUrl($level2Category)?>"><?=$level2Category->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
    </div>
<?php endforeach;?>

<br style="clear: both">
<br style="clear: both">
<h2 style="color: #d91b39;">🔥 <?=$this->translate('Горячие объявления')?> 🔥</h2>
<table class="serp">
<?php foreach ($this->getHotAds(Config::get('HOT_LAYOUT_HOT_ADS')) as $ad) :?>
    <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>
<?php endforeach;?>
</table>
<br style="clear: both">
<br style="clear: both">
<h2>🔔 <?=$this->translate('Новые объявления')?></h2>
<table class="serp">
    <?php foreach ($this->getAds(Config::get('HOT_LAYOUT_NEW_ADS')) as $ad) :?>
        <?php $this->partial('ad_in_list.inc', ['ad' => $ad])?>
    <?php endforeach;?>
</table>

<?php $this->partial('footer.inc', []);