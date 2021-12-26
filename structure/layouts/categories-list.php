<?php

/**
 * @var $this \Palto\Layout
 */
$this->partial('header.inc', [
    'title' => 'Categories',
    'description' => 'Categories',
]);
?>
<h1>Categories</h1>
<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
    <div class="span-d regions"><a href="<?=$this->generateCategoryUrl($level1Category)?>"><strong> <?=$level1Category->getTitle()?></strong></a>
        <?php if ($level2Categories = $this->getWithAdsCategories($level1Category->getId())) :?>
            <ul>
                <?php foreach ($level2Categories as $level2Category) :?>
                    <li>
                        <ul>
                            <a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                <?=$level2Category->getTitle()?>
                            </a>
                            <?php if ($level3Categories = $this->getWithAdsCategories($level2Category->getId())) :?>
                                <ul>
                                <?php foreach ($level3Categories as $level3Category) :?>
                                    <li>
                                        <a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                            <?=$level3Category->getTitle()?>
                                        </a>
                                    </li>
                                <?php endforeach;?>
                                </ul>
                            <?php endif;?>
                        </ul>

                    </li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);