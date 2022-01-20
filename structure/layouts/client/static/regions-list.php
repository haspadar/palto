<?php

/**
 * @var $this \Palto\Layout\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('regions_title'),
    'description' => $this->translate('regions_description'),
]);
?>
<h1><?=$this->translate('regions_h1')?></h1>
<?php foreach ($this->getWithAdsRegions() as $level1Region) :?>
    <div class="span-d regions"><a href="<?=$this->generateRegionUrl($level1Region)?>"><strong> <?=$level1Region->getTitle()?></strong></a>
        <?php if ($level2Regions = $this->getWithAdsRegions($level1Region)) :?>
            <ul>
                <?php foreach ($level2Regions as  $level2Region) :?>
                    <li><a href="<?=$this->generateRegionUrl($level2Region)?>"><?=$level2Region->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);