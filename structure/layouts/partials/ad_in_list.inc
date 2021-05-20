<?php
/**
 * @var $this \Palto\Palto
 */
$ad = $this->getPartialVariable('ad');
?>
<div class="ob-block">
    <div class="img">
        <div class="i0">
            <div class="i1">
                <a href="<?= $this->generateAdUrl($ad) ?>">
                    <?php if ($ad['images']) : ?>
                        <img src="<?= $ad['images'][0]['small'] ?>" alt="<?=$ad['title']?>"/>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
    <div class="bod">
        <div class="t"><a href="<?= $this->generateAdUrl($ad) ?>" class="bread">
                <span><?= $ad['title'] ?></span></a></div>
        <div class="d"><?= $this->generateShortText($ad['text']) ?></div>
        <div class="br">
            <?php foreach ($this->getListAdBreadcrumbUrls($ad) as $breadcrumbKey => $breadcrumbItem) :?>
                <?php if ($breadcrumbKey) :?>
                    <span class="sep">»</span>
                <?php endif;?>
                <a href="<?= $breadcrumbItem['url'] ?>" class="bread"><?= $breadcrumbItem['title'] ?></a>
            <?php endforeach;?>
        </div>
        <div class="p">
            <span class="price2"><b><?= $ad['currency'] ?> <?= $ad['price'] ?></b> </span>
        </div>
        <div>
        </div>
    </div>
</div>