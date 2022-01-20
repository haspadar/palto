<?php
/**
 * @var $this \Palto\Layout\Client
 * @var $ad \Palto\Ad
 */
$ad = $this->getPartialVariable('ad');
?>
<tr>
    <td class="serp_img">
        <?php if ($ad->getImages()) : ?>
            <a href="<?= $this->generateAdUrl($ad) ?>"><img src="<?= $ad->getImages()[0]['small'] ?>" alt="<?=$ad->getTitle()?>"/></a>
        <?php else :?>
            <img src="/img/no-photo.png">
        <?php endif; ?>
    </td>
    <td>
        <div><a href="<?= $this->generateAdUrl($ad) ?>" class="serp_link"><?= $ad->getTitle() ?></a></div>
        <div><?= \Palto\Filter::shortText($ad->getText()) ?></div>
        <div class="serp_bread">
            <?php foreach ($this->getBreadcrumbUrls() as $breadcrumbKey => $breadcrumbItem) :?>
<!--            --><?php //foreach ($this->getBreadcrumbUrls() as $breadcrumbKey => $breadcrumbItem) :?>
                <?php if ($breadcrumbKey) :?>
                    <span class="sep">»</span>
                <?php endif;?>
                <a href="<?= $breadcrumbItem['url'] ?>" class="bread_link"><?= $breadcrumbItem['title'] ?></a>
            <?php endforeach;?>
        </div>
        <?php if ($ad->getPrice() > 0) :?>
            <div class="serp_price">🏷 <?= $ad->getCurrency() ?> <?= number_format($ad->getPrice()) ?></div>
        <?php endif;?>

        <div class="serp_region">📍<a class="bread_link" href="<?= $this->generateRegionUrl($ad->getRegion()) ?>"><?=$ad->getRegion()->getTitle()?></a></div>
    </td>
</tr>