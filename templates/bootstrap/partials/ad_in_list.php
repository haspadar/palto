<?php if (!$this->data['ad']->isDeleted()) : ?>
    <div class="card">
        <div class="card-body">
            <?php if ($this->data['ad']->getImages()) : ?>
                <a href="<?= $this->data['ad']->generateUrl() ?>">
                    <img src="<?= $this->data['ad']->getImages()[0]['small'] ?>"
                         alt="<?= $this->data['ad']->getTitle() ?>"
                         onerror="this.src='/img/no-photo.png'"
                    />
                    <span class="card-title"><?= $this->data['ad']->getTitle() ?></span>
                </a>
            <?php else : ?>
                <img src="/img/no-photo.png">
            <?php endif; ?>

            <p class="card-text">
                <?= \Palto\Filter::shortText($this->data['ad']->getText()) ?>
            </p>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) : ?>
                        <li class="breadcrumb-item">
                            <a href="<?= $breadcrumbItem['url'] ?>">
                                <?=$breadcrumbItem['title']?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>


            <?php if ($this->data['ad']->getPrice() > 0) : ?>
                <div class="serp_price">
                    <i class="bi bi-wallet-fill"></i>
                    <?= number_format($this->data['ad']->getPrice()) ?> <?= $this->data['ad']->getCurrency() ?></div>
            <?php endif; ?>

            <div class="serp_region">
                <i class="bi bi-pin-map"></i>
                <a class="card-link" href="<?= $this->data['ad']->getRegion()->generateUrl() ?>">
                    <?= $this->data['ad']->getRegion()->getTitle() ?>
                </a>
            </div>
        </div>
    </div>

<?php else : ?>
    <!--Skipped deleted AD-->
<?php endif;