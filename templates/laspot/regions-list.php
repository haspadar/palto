<?php /** @var $this League\Plates\Template\Template */

use Palto\Regions; ?>

<?php $this->layout('layout');?>

<?=\Palto\Counters::get('google')?>
<?php foreach (Regions::getWithAdsRegions() as $level1Region) :?>
    <div class="span-d regions"><a href="<?=$level1Region->generateUrl()?>"><strong> <?=$level1Region->getTitle()?></strong></a>
        <?php if ($level2Regions = Regions::getWithAdsRegions($level1Region)) :?>
            <ul>
                <?php foreach ($level2Regions as  $level2Region) :?>
                    <li><a href="<?=$level2Region->generateUrl()?>"><?=$level2Region->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>

        <?php endif;?>
    </div>
<?php endforeach;