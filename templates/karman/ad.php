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
    <dd class="col-sm-9"><?=$this->data['ad']->getCreateTime()->format('d.m.Y H:i:s')?></dd>


    <dt class="col-sm-3">Категория</dt>
    <dd class="col-sm-9">
        <a href="/karman/categories/<?=$this->data['ad']->getCategory()->getId()?>?cache=0">
            <?=$this->data['ad']->getCategory()->getTitle()?>
        </a>
    </dd>

    <dt class="col-sm-3">Текст</dt>
    <dd class="col-sm-9">
        <p>
            <?=$this->data['ad']->getText()?>
        </p>
    </dd>
</dl>

<?php $this->insert('partials/karman-move-ad-modal')?>

<button class="btn btn-secondary move-ad"
        type="button"
        data-ad-id="<?=$this->data['ad']->getId()?>"
        data-category-id="<?=$this->data['ad']->getCategory()->getId()?>"
        data-category-parent-id="<?=$this->data['ad']->getCategory()->getParentId()?>"
>
    Перенести
</button>

<button class="btn btn-secondary btn-dark find-and-move-ad"
        type="button"
        data-ad-id="<?=$this->data['ad']->getId()?>"
        data-category-id="<?=$this->data['ad']->getCategory()->getId()?>"
        data-category-parent-id="<?=$this->data['ad']->getCategory()->getParentId()?>"
>
    Найти по синонимам
</button>

<div class="alert alert-dark find-and-move-ad-report d-none col-6" role="alert">
    <div class="loading">
        <span>Поиск...</span>
        <div class="spinner-border float-end" role="status" aria-hidden="true"></div>
    </div>
    <div class="text"></div>
</div>

