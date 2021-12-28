<?php

/**
 * @var $this \Palto\Palto
 */
$this->partial('header.inc', [
    'title' => 'Ogłoszenia w miastach Polski',
    'description' => 'Tablica ogłoszeń lokalnych w miastach Polski',
]);
?>
<h1>Miasta</h1>
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
<?php foreach ($this->getRegions(0, 1) as $level1Region) :?>
    <div class="span-d regions"><a href="<?=$this->generateRegionUrl($level1Region)?>"><strong> <?=$level1Region['title']?></strong></a>
        <?php if ($level2Regions = $this->getRegions($level1Region['id'])) :?>
            <ul>
                <?php foreach ($level2Regions as  $level2Region) :?>
                    <li><a href="<?=$this->generateRegionUrl($level2Region)?>"><?=$level2Region['title']?></a></li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;?>

<?php $this->partial('footer.inc', []);