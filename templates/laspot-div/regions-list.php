<?php /** @var $this League\Plates\Template\Template */

use Palto\Regions; ?>

<?php $this->layout('layout');?>

<div class="blocks">
    <?php if ($this->data['h1']) :?>
        <div class="blocks__headline headline">
            <h1><?= $this->data['h1'] ?></h1>
        </div>
    <?php endif;?>
</div>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>

<div class="categories">
    <div class="categories__content">
        <?php /** @var $level1Region \Palto\Region */?>
        <?php foreach (Regions::getLiveRegions() as $level1Region) :?>
            <?php /** @var $level1Region \Palto\Region */?>
            <?php $level2Regions = $level1Region->getChildren()?>
            <?php if ($level2Regions) :?>
                <ul class="categories__list">
                    <span class="categories__headline-link">
                        <a href="<?=$level1Region->generateUrl()?>"><?=$level1Region->getTitle()?></a>
                    </span>
                    <?php /** @var $level2Region \Palto\Region */?>
                    <?php foreach ($level2Regions as  $level2Region) :?>
                        <li class="categories__link">
                            <a href="<?=$level2Region->generateUrl()?>">
                                <?=$level2Region->getTitle()?>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        <?php endforeach;?>
    </div>
</div>
