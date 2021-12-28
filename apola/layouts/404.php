<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => '404',
    'description' => '404',
]);
?>
    <br/>
<?php if ($this->getCurrentAd()) :?>
    <h1>Reklama została usunięta</h1>
<?php else :?>
    <h1>Nie znaleziono</h1>
<?php endif;?>


    <h2>Categories</h2>
<?php foreach ($this->getWithAdsCategories(0, 1) as $level1Category) :?>
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
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);