<?php

/**
 * @var $this \Palto\Layout
 */
$this->partial('header.inc', [
    'title' => 'Regions',
    'description' => 'Regions',
]);
?>
<h1>Regions</h1>
<?php foreach ($this->getWithAdsRegions() as $level1Region) :?>
    <div class="span-d regions"><a href="<?=$this->generateRegionUrl($level1Region)?>"><strong> <?=$level1Region->getTitle()?></strong></a>
        <?php if ($level2Regions = $this->getWithAdsRegions($level1Region->getId())) :?>
            <ul>
                <?php foreach ($level2Regions as  $level2Region) :?>
                    <li><a href="<?=$this->generateRegionUrl($level2Region)?>"><?=$level2Region->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);