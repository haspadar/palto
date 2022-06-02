<?php /** @var $this League\Plates\Template\Template */ ?>
<?php use Palto\Categories; ?>
<?php $this->layout('layout'); ?>

<?=\Palto\Counters::get('google') ?: \Palto\Counters::receive('adx')?>

<?php if ($categories = Categories::getLiveCategories($this->data['category'], $this->data['region'])) : ?>
    <div class="categories">
<!--        <div class="categories__headline headline">-->
<!--            <h2>For Sale in New York from Craigslist</h2>-->
<!--        </div>-->
        <div class="categories__content">
            <ul class="categories__list categories__sub-list">
                <?php foreach ($categories as $childCategory) : ?>
                    <?php /** @var $childCategory \Palto\Category */ ?>
                    <li class="categories__link categories__sub-link">
                        <a href="<?= $childCategory->generateUrl($this->data['region']) ?>">
                            <?= $childCategory->getTitle() ?>
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