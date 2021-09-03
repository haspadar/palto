<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Regions',
    'description' => 'Regions',
]);
?>
<h1>Regions</h1>
<?php foreach ($this->getRegions(0, 1) as $level1Region) :?>
    <div class="span-d regions"><a href="<?=$this->generateRegionUrl($level1Region)?>"><strong> <?=$level1Region['title']?></strong></a>
        <?php if ($level2Regions = $this->getRegions($level1Region['id'])) :?>
            <ul>
                <?php foreach ($level2Regions as  $level2Region) :?>
                    <li><a href="<?=$this->generateRegionUrl($level2Region)?>"><?=$level2Region['title']?></a></li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);