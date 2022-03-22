<nav aria-label="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    <ol class="breadcrumb">
        <?php foreach ($this->data['breadcrumbs'] as $breadcrumbKey => $breadcrumbItem) :?>
            <?php if ($breadcrumbItem['url']) :?>
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="<?=$breadcrumbItem['url']?>">
                        <?=$breadcrumbItem['title']?>
                    </a>
                </li>
            <?php else :?>
                <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <?=$breadcrumbItem['title']?>
                </li>
            <?php endif;?>

        <?php endforeach;?>
    </ol>
</nav>