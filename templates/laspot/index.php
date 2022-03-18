<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<?php /** @var $level1Region \Palto\Region*/?>
<?php foreach ($this->data['regions'] as $level1Region) :?>
    <div class="span-d regions">
        <a href="<?=$level1Region->generateUrl()?>"><strong> <?=$level1Region->getTitle()?></strong></a>
    </div>
<?php endforeach;?>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<br style="clear: both">
<br style="clear: both">
<h2><?=$this->translate('Категории')?></h2>

<?php /** @var $level1Category \Palto\Category */?>
<?php foreach (\Palto\Categories::getLiveCategories(null, $this->data['region']) as $level1Category) :?>
    <div class="span-d">
        <p><a href="<?=$level1Category->generateUrl($this->data['region'])?>">
                <?php if ($level1Category->getEmoji()) :?>
                    <?=$level1Category->getEmoji()?>
                <?php elseif ($level1Category->getIconUrl()) :?>
                    <img src="<?=$level1Category->getIconUrl()?>"
                         title="<?=$level1Category->getIconText()?>"
                         class="icm"
                         onerror="this.src='/laspot-theme/img/no-photo.png'"
                    />
                <?php endif?>

                <strong> <?=$level1Category->getTitle()?></strong>
            </a>
        </p>
        <?php /** @var $level2Category \Palto\Category */?>
        <?php if ($level2Categories = $level1Category->getLiveChildren($this->data(['region']))) :?>
            <ul>
                <?php foreach ($level2Categories as  $level2Category) :?>
                    <li><a href="<?=$level2Category->generateUrl($this->data['region'])?>"><?=$level2Category->getTitle()?></a></li>
                <?php endforeach;?>
            </ul>
        <?php endif;?>
    </div>
<?php endforeach;?>

<br style="clear: both">
<br style="clear: both">
<h2 style="color: #d91b39;">🔥 <?=$this->translate('Горячие объявления')?> 🔥</h2>
<table class="serp">
    <?php foreach (Ads::getHotAds($this->data['region'], 5) as $ad) :?>
        <?php $this->insert('partials/ad_in_list', ['ad' => $ad])?>
    <?php endforeach;?>

</table>
<br style="clear: both">
<br style="clear: both">
<h2>🔔 <?=$this->translate('Новые объявления')?></h2>
<table class="serp">
    <?php foreach (Ads::getAds($this->data['region'], $this->data['category'], \Palto\Config::get('HOT_LAYOUT_NEW_ADS')) as $ad) :?>
        <?php $this->insert('partials/ad_in_list', ['ad' => $ad])?>
    <?php endforeach;?>
</table>
