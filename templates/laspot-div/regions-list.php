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
            <ul class="categories__list categories__sub-list">
                <?php foreach (Regions::getLiveRegions() as $level1Region) :?>
                    <li class="categories__link categories__sub-link">
                        <a href="<?= $level1Region->generateUrl() ?>">
                            <?= $level1Region->getTitle() ?>
                        </a>

                        <?php if ($level2Regions = Regions::getLiveRegions($level1Region)) :?>
                            <ul class="categories__list categories__sub-list">
                                <?php foreach ($level2Regions as $level2Region) :?>
                                    <li class="categories__link categories__sub-link">
                                        <a href="<?=$level2Region->generateUrl()?>">
                                            <?= $level2Region->getTitle() ?>
                                        </a>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        <?php endif;?>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
</div>
