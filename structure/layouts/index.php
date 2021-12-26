<?php

/**
 * @var $this \Palto\Layout
 */
$this->partial('header.inc', [
    'title' => 'Private free classifieds in Los Angeles (LA) from craigslist and other boards',
    'description' => 'Aggregator of all classifieds boards in Los Angeles.',
]);
?>
<h1>Categories</h1>
<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
    <div class="span-d">
        <p><a href="<?=$this->generateCategoryUrl($level1Category)?>">
                <?php if ($level1Category->getIconUrl()) :?>
                    <img src="<?=$level1Category->getIconUrl()?>" title="<?=$level1Category->getIconText()?>" class="icm" />
                <?php endif?>
                <strong> <?=$level1Category->getTitle()?></strong>
            </a>
        </p>
        <?php if ($level2Categories = $this->getWithAdsCategories($level1Category->getId())) :?>
            <ul>
                <?php foreach ($level2Categories as  $level2Category) :?>
                    <li><a href="<?=$this->generateCategoryUrl($level2Category)?>"><?=$level2Category->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);