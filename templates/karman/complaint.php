<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');
?>

    <dl>
        <dt>Сообщение:</dt>
        <dd><?=$this->data['complaint']['message']?></dd>
        <dt>Объяление:</dt>
        <dd>
            <a href="<?=$this->data['ad']->generateUrl()?>" target="_blank" class="text-decoration-none">
                <?=\Palto\Filter::shortText($this->data['ad']->getTitle(), 100)?>
            </a>
        </dd>
        <dt>Почта:</dt>
        <dd>
            <span id="email"><?=$this->data['complaint']['email']?></span>
            <button class="btn btn-outline-secondary btn-sm copy" data-text="<?=$this->data['complaint']['email']?>">Скопировать</button>
        </dd>
        <dt>Время:</dt>
        <dd>
            <?php $createTime = new DateTime($this->data['complaint']['create_time'])?>
            <?=$createTime->format('d')?> <?=\Palto\Russian::month($createTime->format('m'))?>
            <small class="text-muted"><?=$createTime->format('H:i')?></small>
        </dd>
        <dt>IP:</dt>
        <dd><?=$this->data['complaint']['ip']?></dd>
    </dl>

    <button type="button" class="btn btn-warning ignore-complaint" data-id="<?=$this->data['complaint']['id']?>">Игнорировать жалобу</button>
    <button type="button" class="btn btn-danger remove-ad" data-id="<?=$this->data['complaint']['id']?>">Удалить объявление</button>

    <div class="alert alert-danger mt-2 fade" role="alert">

    </div>