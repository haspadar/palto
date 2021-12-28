<?php
/**
 * @var $this \Palto\Palto
 */
$ad = $this->getPartialVariable('ad');
?>
<tr>
    <td class="serp_img">
        <?php if ($ad['images']) : ?>
            <a href="<?= $this->generateAdUrl($ad) ?>"><img src="<?= $ad['images'][0]['small'] ?>;s=100x100" alt="<?=$ad['title']?>"/></a>
        <?php else :?>
            <img src="/img/no-photo.png">
        <?php endif; ?>
    </td>
    <td>
        <div><a href="<?= $this->generateAdUrl($ad) ?>" class="serp_link"><?= $ad['title'] ?></a></div>
        <div><?= $this->generateShortText($ad['text']) ?></div>
        <div class="serp_bread">
            <?php foreach ($this->getListAdBreadcrumbUrls($ad) as $breadcrumbKey => $breadcrumbItem) :?>
                <?php if ($breadcrumbKey) :?>
                    <span class="sep">»</span>
                <?php endif;?>
                <a href="<?= $breadcrumbItem['url'] ?>" class="bread_link"><?= $breadcrumbItem['title'] ?></a>
            <?php endforeach;?>
        </div>
        <?php if ($ad['price'] > 0) :?>
            <div class="serp_price">🏷<?= number_format($ad['price']) ?> <?= $ad['currency'] ?> </div>
        <?php endif;?>

        <div class="serp_region">📍<a class="bread_link" href="<?= $this->generateRegionUrl($ad['region']) ?>"><?=$ad['region']['title']?></a></div>
    </td>
</tr>