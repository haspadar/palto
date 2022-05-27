<?php if (!$this->data['ad']->isDeleted()) :?>
    <div class="hot-ads__item ad">
        <a href="<?= $this->data['ad']->generateUrl() ?>" class="ad__image">
            <?php if ($this->data['ad']->getImages()) : ?>
                <a href="<?= $this->data['ad']->generateUrl() ?>">
                    <img src="<?= $this->data['ad']->getImages()[0]['small'] ?>"
                         alt="<?=$this->data['ad']->getTitle()?>"
                         onerror="this.src='/img/no-photo.png'"
                    /></a>
            <?php else :?>
                <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/mini-ad.jpg")?>">
            <?php endif; ?>
            
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