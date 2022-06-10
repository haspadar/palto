<?php /** @var $this League\Plates\Template\Template */?>

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
                <?php foreach (\Palto\Categories::getLiveCategories() as $level1Category) :?>
                    <li class="categories__link categories__sub-link">
                        <a href="<?= $level1Category->generateUrl($this->data['region']) ?>">
                            <?php if ($level1Category->getEmoji()) :?>
                                <?=$level1Category->getEmoji()?>
                            <?php elseif ($level1Category->getIconUrl()) :?>
                                <img src="<?=$level1Category->getIconUrl()?>"
                                     title="<?=$level1Category->getIconText()?>"
                                     class="icm" />
                            <?php endif?>

                            <?= $level1Category->getTitle() ?>
                        </a>

                        <?php if ($level2Categories = $level1Category->getLiveChildren($this->data['region'])) :?>
                            <ul class="categories__list categories__sub-list">
                                <?php foreach ($level2Categories as $level2Category) :?>
                                    <li class="categories__link categories__sub-link">
                                        <a href="<?= $level2Category->generateUrl($this->data['region']) ?>">
                                            <?php if ($level2Category->getEmoji()) :?>
                                                <?=$level2Category->getEmoji()?>
                                            <?php elseif ($level2Category->getIconUrl()) :?>
                                                <img src="<?=$level2Category->getIconUrl()?>"
                                                     title="<?=$level2Category->getIconText()?>"
                                                     class="icm" />
                                            <?php endif?>

                                            <?= $level2Category->getTitle() ?>
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
