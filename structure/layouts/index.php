<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Page Example Title',
    'description' => 'Page Example Description',
]);
?>
    <br/>
    <h2>Regions</h2>
    <?php foreach ($this->getRegions(0, 1) as $level1Region) :?>
        <div class="span-d">
            <p>
                <a href="<?=$this->generateRegionUrl($level1Region)?>">
                    <strong> <?=$level1Region['title']?></strong>
                </a>
            </p>
            <?php if ($level2Regions = $this->getRegions($level1Region['id'])) :?>
                <ul>
                    <?php foreach ($level2Regions as  $level2Region) :?>
                        <li><a href="<?=$this->generateRegionUrl($level2Region)?>">
                                <?=$level2Region['title']?>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        </div>
    <?php endforeach;?>

    <br style="clear: both">
    <h2>Categories</h2>
    <?php foreach ($this->getCategories(0, 1) as $level1Category) :?>
        <div class="span-d">
            <p>
                <a href="<?=$this->generateCategoryUrl($level1Category)?>">
                    <?php if ($level1Category['icon_url']) :?>
                        <img src="<?=$level1Category['icon_url']?>"
                             title="<?=$level1Category['icon_text']?>"
                             class="icm" />
                    <?php endif?>
                    <strong> <?=$level1Category['title']?></strong>
                </a>
            </p>
            <?php if ($level2Categories = $this->getCategories($level1Category['id'])) :?>
                <ul>
                    <?php foreach ($level2Categories as  $level2Category) :?>
                        <li><a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                <?=$level2Category['title']?>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
        </div>
    <?php endforeach;?>

<?php $this->partial('footer.inc', []);