<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Kategorie ogłoszeń w Polsce',
    'description' => 'Wszystkie kategorie ogłoszeń prywatnych w polskich miastach',
]);
?>
<h1>Kategorie</h1>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4251434934288140"
     crossorigin="anonymous"></script>
<!-- apola_adaptive -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-4251434934288140"
     data-ad-slot="5199190551"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
<?php foreach ($this->getWithAdsCategories(0, 1) as $level1Category) :?>
    <div class="span-d regions"><a href="<?=$this->generateCategoryUrl($level1Category)?>"><strong> <?=$level1Category['title']?></strong></a>
        <?php if ($level2Categories = $this->getWithAdsCategories($level1Category['id'])) :?>
            <ul>
                <?php foreach ($level2Categories as $level2Category) :?>
                    <li>
                        <ul>
                            <a href="<?=$this->generateCategoryUrl($level2Category)?>">
                                <?=$level2Category['title']?>
                            </a>
                            <?php if ($level3Categories = $this->getWithAdsCategories($level2Category['id'])) :?>
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