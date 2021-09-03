<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Categories',
    'description' => 'Categories',
]);
?>
<h1>Categories</h1>
<?php foreach ($this->getCategories(0, 1) as $level1Category) :?>
    <div class="span-d regions"><a href="<?=$this->generateCategoryUrl($level1Category)?>"><strong> <?=$level1Category['title']?></strong></a>
        <?php if ($level2Categories = $this->getCategories($level1Category['id'])) :?>
            <ul>
                <?php foreach ($level2Categories as $level2Category) :?>
                    <li>
                        <ul>
                            <a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                <?=$level2Category['title']?>
                            </a>
                            <?php if ($level3Categories = $this->getCategories($level2Category['id'])) :?>
                                <ul>
                                <?php foreach ($level3Categories as $level3Category) :?>
                                    <li>
                                        <a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                            <?=$level3Category['title']?>
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