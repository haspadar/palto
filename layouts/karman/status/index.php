<?php

/**
 * @var $this \Palto\Layout\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Client\Karman
 */
$this->partial('header.inc', [
    'title' => 'Статус',
]);
?>

<dl>
    <dt>Сайт включен</dt>
    <dd>
        <?php if (\Palto\Status::isSiteEnabled()) :?>
            <span class="badge rounded-pill bg-success">Да</span>
            <a href="javascript:void(0);" class="small disable-site text-decoration-none">Выключить</a>
        <?php else :?>
            <span class="badge rounded-pill bg-danger">Нет</span>
            <a href="javascript:void(0);" class="small enable-site text-decoration-none">Включить</a>
        <?php endif;?>
    </dd>
    <dt>Кеш включен</dt>
    <dd>
        <?php if (\Palto\Status::isCacheEnabled()) :?>
            <span class="badge rounded-pill bg-success">Да</span>
            <a href="javascript:void(0);" class="small disable-cache text-decoration-none">Выключить</a>
        <?php else :?>
            <span class="badge rounded-pill bg-danger">Нет</span>
            <a href="javascript:void(0);" class="small enable-cache text-decoration-none">Включить</a>
        <?php endif;?>
    </dd>
    <dt>Занятое место на диске</dt>
    <dd><?=\Palto\Status::getDirectoryUsePercent('/')?></dd>
</dl>

<?php $this->partial('footer.inc');