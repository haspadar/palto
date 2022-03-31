<?php /** @var $this League\Plates\Template\Template */?>
<?php use Palto\Ads;
use Palto\Config; ?>
<?php $this->layout('layout');?>

<?php if ($this->data['regions']) :?>
    <div class="">
    <?php foreach ($this->data['regions'] as $region) :?>
            <i class="bi bi-pin-map-fill"></i>
            <a href="<?=$region->generateUrl()?>"><?=$region->getTitle()?></a>
    <?php endforeach;?>
    </div>
<?php endif;?>


<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<br>
<br>

<?php /** @var $level1Category \Palto\Category */?>
<?php if (!\Palto\Strategy::isSingleCategory()) :?>
    <?php if ($this->data['regions']) :?>
        <h2><i class="bi bi-briefcase"></i>
            <?=$this->translate('Категории')?></h2>
    <?php endif;?>

    <div class="d-flex">
    <?php foreach (\Palto\Categories::getLiveCategoriesWithChildren(
        Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_1'),
        Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2')
    ) as $level1Category) :?>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?=$level1Category->generateUrl($this->data['region'])?>">
                    <?php if ($level1Category->getEmoji()) :?>
                        <?=$level1Category->getEmoji()?>
                    <?php elseif ($level1Category->getIconUrl()) :?>
                        <img src="<?=$level1Category->getIconUrl()?>"
                             title="<?=$level1Category->getIconText()?>"
                             class="icm"
                        />
                    <?php endif?>
                </a>
            </li>
            <?php if ($level2Categories = $level1Category->getLiveChildren($this->data['region'], Config::get('HOT_LAYOUT_CATEGORIES_LEVEL_2'))) :?>
                    <?php foreach ($level2Categories as  $level2Category) :?>
                        <li class="list-group-item"><a href="<?=$level2Category->generateUrl($this->data['region'])?>"><?=$level2Category->getTitle()?></a></li>
                    <?php endforeach;?>

                    <li class="list-group-item"><a href="<?=$level1Category->generateUrl($this->data['region'])?>">Other...</a></li>
            <?php endif;?>

        </ul>

    <?php endforeach;?>
</div>
<?php endif?>

<br>
<br>

<h2>
    <i class="bi bi-asterisk"></i>

    <?=$this->translate('Горячие объявления')?> </h2>
<table class="serp">
    <?php foreach (Ads::getHotAds($this->data['region'], 5) as $ad) :?>
        <?php $this->insert('partials/ad_in_list', ['ad' => $ad])?>
    <?php endforeach;?>

</table>
<br>
<br>
<h2><i class="bi bi-bell"></i>
    <?=$this->translate('Новые объявления')?></h2>
<table class="serp">
    <?php foreach (Ads::getAds($this->data['region'], $this->data['category'], \Palto\Config::get('HOT_LAYOUT_NEW_ADS')) as $ad) :?>
        <?php $this->insert('partials/ad_in_list', ['ad' => $ad])?>
    <?php endforeach;?>
</table>