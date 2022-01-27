<?php

/**
 * @var $this \Palto\Layout\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('index_title'),
    'description' => $this->translate('index_description'),
]);
?>
<h1><?=$this->translate('index_h1')?></h1>
<?php $regionsLimit = \Palto\Config::get('INDEX_LAYOUT_REGIONS');?>
<?php \Palto\Debug::dump($regionsLimit)?>
<?php if (!is_numeric($regionsLimit) || intval($regionsLimit) > 0) :?>
    <?php foreach ($this->getWithAdsRegions(null, intval($regionsLimit)) as $level1Region) :?>
        <div class="span-d regions">
            <a href="<?=$this->generateRegionUrl($level1Region)?>"><strong> <?=$level1Region->getTitle()?></strong></a>
        </div>
    <?php endforeach;?>
<?php endif?>

<?=\Palto\Counters::get('google')?>
<br style="clear: both">
<br style="clear: both">
<h2><?=$this->translate('Категории')?></h2>

<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
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
        <?php if ($level2Categories = $this->getWithAdsCategories($level1Category)) :?>
            <ul>
                <?php foreach ($level2Categories as  $level2Category) :?>
                    <li><a href="<?=$this->generateCategoryUrl($level2Category)?>"><?=$level2Category->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);