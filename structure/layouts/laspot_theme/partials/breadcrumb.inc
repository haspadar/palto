<div class="bread" itemscope itemtype="http://schema.org/BreadcrumbList">
<?php $breadcrumbUrls = $this->getPartialVariable('breadcrumbUrls')?>
<?php foreach ($breadcrumbUrls as $breadcrumbKey => $breadcrumbItem) :?>
    <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        <?php if ($breadcrumbItem['url']) :?>
            <a itemprop="item" href="<?=$breadcrumbItem['url']?>" class="bread_link">
                <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
            </a>
        <?php else :?>
            <span itemprop="name"><?= $breadcrumbItem['title'] ?></span>
        <?php endif;?>
        <meta itemprop="position" content="<?=$breadcrumbKey + 1?>"/>
    </span>

    <?php $isLast = !isset($breadcrumbUrls[$breadcrumbKey + 1]);?>
    <?php if (!$isLast) :?>
        <span class="sep">»</span>
    <?php endif;?>

<?php endforeach;?>
</div>