<div class="city" itemscope itemtype="http://schema.org/BreadcrumbList">
    <div class="city__content">
        <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) :?>
            <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <?php if ($breadcrumbItem['url']) :?>
                    <a itemprop="item" href="<?=$breadcrumbItem['url']?>">
                        <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
                    </a>
                <?php else :?>
                    <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
                <?php endif;?>
                <meta itemprop="position" content="<?=$breadcrumbKey + 1?>"/>

            <?php $isLast = !isset($this->data['breadcrumbs'][$breadcrumbKey + 1]);?>
            <?php if (!$isLast) :?>
                <span>»</span>
            <?php endif;?>
        <?php endforeach;?>
    </span>
</div>