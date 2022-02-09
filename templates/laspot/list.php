<?php /** @var $this League\Plates\Template\Template */ ?>
<?php use Palto\Categories; ?>
<?php $this->layout('layout'); ?>

<?php $flashMessage = \Palto\Flash::receive(); ?>
<?php /** @var $this League\Plates\Template\Template */ ?>

<?= \Palto\Counters::get('google') ?>

<?php if ($categories = Categories::getLiveCategories($category ?? null, $region)) : ?>
    <ul class="sub_cat">

        <?php foreach ($categories as $childCategory) : ?>
            <?php /** @var $childCategory \Palto\Category */ ?>
            <li><a href="<?= $childCategory->generateUrl($region) ?>"><?= $childCategory->getTitle() ?></a></li>
        <?php endforeach ?>
    </ul>
<?php endif; ?>

    <div class="region_cat"><b><?= $this->translate('Регион') ?>:</b> <?= $region->getTitle() ?></div>
    <table class="serp">
        <?php foreach ($ads as $adIndex => $ad) : ?>
            <?php $this->insert('partials/ad_in_list', ['ad' => $ad]) ?>
            <?php if (in_array($adIndex + 1, [5, 15])) : ?>
                <tr>
                    <td colspan="2"><?= \Palto\Counters::get('google') ?></td>
                </tr>
            <?php elseif (in_array($adIndex + 1, [2, 10, 21])) : ?>
                <tr>
                    <td colspan="2"><?= \Palto\Counters::get('google') ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?= $this->insert('partials/pager') ?>