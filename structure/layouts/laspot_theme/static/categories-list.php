<?php

/**
 * @var $this \Palto\Layout\Client
 */
$this->partial('header.inc', [
    'title' => $this->translate('categories_title'),
    'description' => $this->translate('categories_description'),
]);
?>
<h1><?=$this->translate('categories_h1')?></h1>
<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<?php foreach ($this->getWithAdsCategories() as $level1Category) :?>
    <div class="span-d regions"><a href="<?=$this->generateCategoryUrl($level1Category)?>"><strong> <?=$level1Category->getTitle()?></strong></a>
        <?php if ($level2Categories = $this->getWithAdsCategories($level1Category)) :?>
            <ul>
                <?php foreach ($level2Categories as $level2Category) :?>
                    <li>
                        <ul>
                            <a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                <?=$level2Category->getTitle()?>
                            </a>
                            <?php if ($level3Categories = $this->getWithAdsCategories($level2Category)) :?>
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