<?php

/**
 * @var $this \Palto\Layout\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client
 */
$this->partial('header.inc', [
    'title' => '404',
    'description' => '404',
]);
?>
    <br/>
<?php if ($this->getAd()) :?>
    <h1><?=$this->translate('Объявление было удалено')?></h1>
<?php else :?>
    <h1><?=$this->translate('Не найдено')?></h1>
<?php endif;?>

<h2><?=$this->translate('Категории')?></h2>
<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
    <div class="span-d">
        <p>
            <a href="<?=$this->generateCategoryUrl($level1Category)?>">
                <?php if ($level1Category->getIconUrl()) :?>
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