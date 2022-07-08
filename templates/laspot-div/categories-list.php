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
        <?php /** @var $level1Category \Palto\Category */?>
        <?php foreach (\Palto\Categories::getLiveCategories() as $level1Category) :?>
            <?php /** @var $level2Category \Palto\Category */?>
            <?php $level2Categories = $level1Category->getLiveChildren($this->data(['region']))?>
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
                                 onerror="this.src='/themes/laspot-div/img/no-photo.png'"
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
                </ul>
            <?php endif;?>
        <?php endforeach;?>
    </div>
</div>
