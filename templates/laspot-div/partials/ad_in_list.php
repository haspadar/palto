<?php if (!$this->data['ad']->isDeleted()) :?>
    <div class="hot-obs__item ob">
        <a href="<?= $this->data['ad']->generateUrl() ?>" class="ob__image">
            <?php if ($this->data['ad']->getImages()) : ?>
                <img src="<?= $this->data['ad']->getImages()[0]['small'] ?>"
                     alt="<?=$this->data['ad']->getTitle()?>"
                     onerror="this.src='<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/no-pets.png")?>'"
                />
            <?php else :?>
                <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/no-pets.png")?>">
            <?php endif; ?>

        </a>
        <div class="ob__info">
            <a href="<?= $this->data['ad']->generateUrl() ?>" class="ob__headline">
                <?= $this->data['ad']->getTitle() ?>
            </a>
            <p class="ob__text">
                <?= \Palto\Filter::shortText($this->data['ad']->getText()) ?>
            </p>

            <?php if ($this->data['ad']->getPrice() > 0) :?>
                <div class="">🏷 <?= number_format($this->data['ad']->getPrice()) ?> <?= $this->data['ad']->getCurrency() ?></div>
            <?php endif;?>

            <?php if (($region = $this->data['ad']->getRegion()) && $this->data['ad']->getCategory()) :?>
                <div class="ob__block">
                    <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/icon-block.png")?>" alt="location">
                    <a href="<?= $this->data['ad']->getCategory()->generateUrl($this->data['ad']->getRegion()) ?>">
                        <?=$this->data['ad']->getCategory()->getTitle()?> <?=$this->translate('в')?> <?= $region->getTitle() ?>
                    </a>
                </div>
            <?php elseif (($region = $this->data['ad']->getRegion()) && !$this->data['ad']->getCategory()) :?>
                <div class="ob__block">
                    <img src="<?=$this->asset(\Palto\Directory::getThemePublicDirectory() . "/img/icon-block.png")?>" alt="location">
                    <?=$this->translate('в')?> <?= $region->getTitle() ?>
                </div>
            <?php endif;?>
        </div>
    </div>
<?php else : ?>
<!--Skipped deleted AD-->
<?php endif;