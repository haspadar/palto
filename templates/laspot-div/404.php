<?php /** @var $this League\Plates\Template\Template */?>
<?php $this->layout('layout');?>

<div class="blocks">
    <?php if ($this->data['h1']) :?>
        <div class="blocks__headline headline">
            <h1><?= $this->data['h1'] ?></h1>
        </div>
    <?php endif;?>

</div>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
<h2><?=$this->data['h2']?></h2>
<?php foreach (\Palto\Categories::getLiveCategories($this->data['category'], $this->data['region']) as $level1Category) :?>
    <div class="span-d">
        <p>
            <a href="<?=$level1Category->generateUrl($this->data['region'])?>">
                <?php if ($level1Category->getEmoji()) :?>
                    <?=$level1Category->getEmoji()?>
                <?php elseif ($level1Category->getIconUrl()) :?>
                    <img src="<?=$level1Category->getIconUrl()?>"
                         title="<?=$level1Category->getIconText()?>"
                         class="icm" />
                <?php endif?>

                <strong> <?=$level1Category->getTitle()?></strong>
            </a>
        </p>
    </div>
<?php endforeach;