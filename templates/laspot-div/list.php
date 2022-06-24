<?php /** @var $this League\Plates\Template\Template */ ?>
<?php use Palto\Categories; ?>
<?php $this->layout('layout'); ?>

<div class="blocks">
    <?php if ($this->data['h1']) :?>
        <div class="blocks__headline headline">
            <h1><?= $this->data['h1'] ?></h1>
        </div>
    <?php endif;?>

</div>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>

<?php if ($categories = Categories::getLiveCategories($this->data['category'], $this->data['region'], 0, 'title ASC')) : ?>
    <div class="categories">
        <div class="categories__content">
            <ul class="categories__list categories__sub-list">
                <?php foreach ($categories as $childCategory) : ?>
                    <?php /** @var $childCategory \Palto\Category */ ?>
                    <li class="categories__link categories__sub-link">
                        <a href="<?= $childCategory->generateUrl($this->data['region']) ?>">
                            <?php if ($childCategory->getEmoji()) :?>
                                <?=$childCategory->getEmoji()?>
                            <?php elseif ($childCategory->getIconUrl()) :?>
                                <img src="<?=$childCategory->getIconUrl()?>"
                                     title="<?=$childCategory->getIconText()?>"
                                     class="icm" />
                            <?php endif?>

                            <?= $childCategory->getTitle() ?>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
<?php endif;?>

<?php if ($regions = \Palto\Regions::getLiveRegions($this->data['region'])) : ?>
    <div class="headline">
        <h2>
            <?php if ($this->data['region']->getLevel() == 0) :?>
                <?=$this->translate('Штаты')?>
            <?php elseif ($this->data['region']->getLevel() == 1) :?>
                <?=$this->translate('Города')?>
            <?php elseif ($this->data['region']->getLevel() == 2) :?>
                <?=$this->translate('Районы')?>
            <?php endif;?>
        </h2>
    </div>
    <div class="categories">
        <div class="categories__content">
            <ul class="categories__list categories__sub-list">
                <?php foreach ($regions as $childRegion) : ?>
                    <?php /** @var $childRegion \Palto\Region */ ?>
                    <li class="categories__link categories__sub-link">
                        <a href="<?= $childRegion->generateUrl() ?>">
                            <?= $childRegion->getTitle() ?>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
<?php endif;?>


<div class="city">
    <div class="city__content">
        <span class="city__region"><?= $this->translate('Регион') ?>:</span>
        <?= $this->data['region']->getTitle() ?>
    </div>
</div>

<div class="new-obs">
        <div class="new-obs__content">
            <div class="new-obs__items">
                <?php foreach ($this->data['ads'] as $adIndex => $ad) : ?>
                    <?php $this->insert('partials/ad_in_list', ['ad' => $ad]) ?>
                    <?php if (in_array($adIndex + 1, [5, 15, 21])) : ?>
                        <?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>
                    <?php elseif (in_array($adIndex + 1, [2, 10])) : ?>
                        <?=\Palto\Counters::receive('adx') ?: \Palto\Counters::get('google')?>
                    <?php endif; ?>
                <?php endforeach;?>
            </div>
            <?= $this->insert('partials/pager') ?>
        </div>
</div>