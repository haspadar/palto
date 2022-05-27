<div class="city" itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) :?>
        <div class="city__content" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">

            <?php if ($breadcrumbItem['url']) :?>
                <a itemprop="item" href="<?=$breadcrumbItem['url']?>" class="bread_link">
                    <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
                </a>
            <?php else :?>
                <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
            <?php endif;?>
            <meta itemprop="position" content="<?=$breadcrumbKey + 1?>"/>

        </div>

        <?php $isLast = !isset($this->data['breadcrumbs'][$breadcrumbKey + 1]);?>
        <?php if (!$isLast) :?>
            <span class="sep">»</span>
        <?php endif;?>
    <?php endforeach;?>
</div>