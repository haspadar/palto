<?php

/**
 * @var $this \Palto\Layout\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('index_title'),
    'description' => $this->translate('index_description'),
]);
?>
<h1><?=$this->translate('Категории')?></h1>
<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
    <div class="span-d">
        <p><a href="<?=$this->generateCategoryUrl($level1Category)?>">
                <?php if ($level1Category->getIconUrl()) :?>
                    <img src="<?=$level1Category->getIconUrl()?>" title="<?=$level1Category->getIconText()?>" class="icm" />
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