<?php if (!$this->data['ad']->isDeleted()) :?>
    <div class="hot-ads__item ad">
        <a href="#" class="ad__image">
            <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/mini-ad.jpg")?>" alt="item">
        </a>
        <div class="ad__info">
            <a href="<?= $this->data['ad']->generateUrl() ?>" class="ad__headline">
                <?= $this->data['ad']->getTitle() ?>
            </a>
            <p class="ad__text">
                <?= \Palto\Filter::shortText($this->data['ad']->getText()) ?>
            </p>

            <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) :?>
                <?php if ($breadcrumbKey) :?>
                    <span class="">»</span>
                <?php endif;?>

                <a href="<?= $breadcrumbItem['url'] ?>" class="ad__city"><?= $breadcrumbItem['title'] ?></a>
            <?php endforeach;?>

            <?php if ($this->data['ad']->getPrice() > 0) :?>
                <div class="">🏷 <?= number_format($this->data['ad']->getPrice()) ?> <?= $this->data['ad']->getCurrency() ?></div>
            <?php endif;?>

            <?php if ($region = $this->data['ad']->getRegion()) :?>
                <div class="ad__block">
                    <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/icon-block.png")?>" alt="location">
                    <a href="<?= $region->generateUrl() ?>"><?= $region->getTitle() ?></a>
                </div>

            <?php endif;?>
        </div>
    </div>
<?php else : ?>
<!--Skipped deleted AD-->
<?php endif;