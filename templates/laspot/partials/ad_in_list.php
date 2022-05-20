<?php if (!$this->data['ad']->isDeleted()) :?>
    <tr>
        <td class="serp_img">
            <?php if ($this->data['ad']->getImages()) : ?>
                <a href="<?= $this->data['ad']->generateUrl() ?>">
                    <img src="<?= $this->data['ad']->getImages()[0]['small'] ?>"
                         alt="<?=$this->data['ad']->getTitle()?>"
                         onerror="this.src='/img/no-photo.png'"
                    /></a>
            <?php else :?>
                <img src="/img/no-photo.png">
            <?php endif; ?>
        </td>
        <td>
            <div><a href="<?= $this->data['ad']->generateUrl() ?>" class="serp_link"><?= $this->data['ad']->getTitle() ?></a></div>
            <div><?= \Palto\Filter::shortText($this->data['ad']->getText()) ?></div>
            <div class="serp_bread">
                <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) :?>
                    <?php if ($breadcrumbKey) :?>
                        <span class="sep">»</span>
                    <?php endif;?>
                    <a href="<?= $breadcrumbItem['url'] ?>" class="bread_link"><?= $breadcrumbItem['title'] ?></a>
                <?php endforeach;?>
            </div>
            <?php if ($this->data['ad']->getPrice() > 0) :?>
                <div class="serp_price">🏷 <?= number_format($this->data['ad']->getPrice()) ?> <?= $this->data['ad']->getCurrency() ?></div>
            <?php endif;?>

            <?php if ($region = $this->data['ad']->getRegion()) :?>
                <div class="serp_region">
                    <i class="bi bi-pin-map"></i>
                    <a class="card-link" href="<?= $region->generateUrl() ?>">
                        <?= $region->getTitle() ?>
                    </a>
                </div>
            <?php endif;?>
        </td>
    </tr>
<?php else :?>
    <!--Skipped deleted AD-->
<?php endif;