<?php if (!$this->data['ad']->isDeleted()) :?>
    <div class="hot-obs__item ob">
        <a href="<?= $this->data['ad']->generateUrl() ?>" class="ob__image">
            <?php if ($this->data['ad']->getImages()) : ?>
                <img src="<?= $this->data['ad']->getImages()[0]['small'] ?>"
                     alt="<?=$this->data['ad']->getTitle()?>"
                     onerror="this.src='<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/mini-ob.jpg")?>'"
                />
            <?php else :?>
                <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/mini-ob.jpg")?>">
            <?php endif; ?>

        </a>
        <div class="ob__info">
            <a href="<?= $this->data['ad']->generateUrl() ?>" class="ob__headline">
                <?= $this->data['ad']->getTitle() ?>
            </a>
            <p class="ob__text">
                <?= \Palto\Filter::shortText($this->data['ad']->getText()) ?>
            </p>

            <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) :?>
                <?php if ($breadcrumbKey) :?>
                    <span class="">»</span>
                <?php endif;?>

                <a href="<?= $breadcrumbItem['url'] ?>" class="ob__city"><?= $breadcrumbItem['title'] ?></a>
            <?php endforeach;?>

            <?php if ($this->data['ad']->getPrice() > 0) :?>
                <div class="">🏷 <?= number_format($this->data['ad']->getPrice()) ?> <?= $this->data['ad']->getCurrency() ?></div>
            <?php endif;?>

            <?php if ($region = $this->data['ad']->getRegion()) :?>
                <div class="ob__block">
                    <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/icon-block.png")?>" alt="location">
                    <a href="<?= $region->generateUrl() ?>"><?= $region->getTitle() ?></a>
                </div>

            <?php endif;?>
        </div>
    </div>
<?php else : ?>
<!--Skipped deleted AD-->
<?php endif;