<?php

/**
 * @var $this \Palto\Layout\Karman
 */
$complaint = \Palto\Complaints::getComplaint($this->getId());
$ad = \Palto\Ads::getById($complaint['id']);
$this->partial('header.inc', [
    'title' => 'Жалоба #' . $complaint['id'],
    'breadcrumbUrls' => [[
        'title' => 'Жалобы',
        'url' => '/karman/complaints'
    ], [
        'title' => 'Жалоба #' . $complaint['id'],
    ]]
]);
?>

    <dl>
        <dt>Сообщение:</dt>
        <dd><?=$complaint['message']?></dd>
        <dt>Объяление:</dt>
        <dd>
            <a href="<?=$ad->generateUrl()?>" target="_blank" class="text-decoration-none">
                <?=\Palto\Filter::shortText($ad->getTitle(), 100)?>
            </a>
        </dd>
        <dt>Почта:</dt>
        <dd>
            <span id="email"><?=$complaint['email']?></span>
            <button class="btn btn-outline-secondary btn-sm copy" data-text="<?=$complaint['email']?>">Скопировать</button>
        </dd>
        <dt>Время:</dt>
        <dd>
            <?php $createTime = new DateTime($complaint['create_time'])?>
            <?=$createTime->format('d')?> <?=\Palto\Time::russianMonth($createTime->format('m'))?>
            <small class="text-muted"><?=$createTime->format('H:i')?></small>
        </dd>
        <dt>IP:</dt>
        <dd><?=$complaint['ip']?></dd>
    </dl>

    <button type="button" class="btn btn-warning ignore-complaint">Игнорировать жалобу</button>
    <button type="button" class="btn btn-danger remove-ad">Удалить объявление</button>

    <div class="alert alert-danger mt-2 fade" role="alert">

    </div>

<?php $this->partial('footer.inc');