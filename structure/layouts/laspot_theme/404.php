<?php

/**
 * @var $this \Palto\Layout\Client
 */
$this->partial('header.inc', [
    'title' => '404',
    'description' => '404',
]);
?>
    <br/>
<?php if ($this->getAd()) :?>
    <h1><?=$this->translate('404_h1_ad')?></h1>
<?php else :?>
    <h1><?=$this->translate('404_h1_list')?></h1>
<?php endif;?>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::get('adx')?>

<h2><?=$this->translate('404_h2')?></h2>
<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
    <div class="span-d">
        <p>
            <a href="<?=$this->generateCategoryUrl($level1Category)?>">
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
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);