<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout'); ?>

<dl class="row">
    <dt class="col-sm-3">Заголовок</dt>
    <dd class="col-sm-9">
        <a href="<?=$this->data['ad']->getUrl()?>" target="_blank">
            <?=$this->data['ad']->getTitle()?>
        </a>
    </dd>

    <dt class="col-sm-3">Дата парсинга</dt>
    <dd class="col-sm-9 text-muted"><?=$this->data['ad']->getCreateTime()->format('d.m.Y H:i:s')?></dd>


    <dt class="col-sm-3">Категория</dt>
    <dd class="col-sm-9">
        <a href="/karman/categories/<?=$this->data['ad']->getCategory()->getId()?>?cache=0">
            <?=$this->data['ad']->getCategoryPath()?>
        </a>
    </dd>

    <dt class="col-sm-3">Текст</dt>
    <dd class="col-sm-9">
        <p>
            <?=$this->data['ad']->getText()?>
        </p>
        <?php if ($this->data['ad']->getImages()) :?>
            <p>
                <?php foreach ($this->data['ad']->getImages() as $image) :?>
                    <a href="<?=$image['big']?>" target="_blank">
                        <img src="<?=$image['small']?>" style="max-width:100px" loading="lazy">
                    </a>
                <?php endforeach;?>
            </p>
        <?php endif;?>
    </dd>
</dl>

<?php $this->insert('partials/karman-move-ad-modal')?>

<button class="btn btn-secondary btn-dark find-and-move-ad"
        type="button"
        data-ad-id="<?=$this->data['ad']->getId()?>"
        data-category-id="<?=$this->data['ad']->getCategory()->getId()?>"
        data-category-parent-id="<?=$this->data['ad']->getCategory()->getParentId()?>"
>
    Найти по синонимам
</button>

<button class="btn btn-secondary move-ad"
        type="button"
        data-ad-id="<?=$this->data['ad']->getId()?>"
        data-category-id="<?=$this->data['ad']->getCategory()->getId()?>"
        data-category-parent-id="<?=$this->data['ad']->getCategory()->getParentId()?>"
>
    Перенести
</button>


<div class="alert alert-dark find-and-move-ad-report d-none col-6" role="alert">
    <div class="loading">
        <span>Поиск...</span>
        <div class="spinner-border float-end" role="status" aria-hidden="true"></div>
    </div>
    <div class="text"></div>
</div>
