<?php

/**
 * @var $this \Palto\Layout\Karman
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
            <a href="javascript:void(0);" class="small disable-site">Выключить</a>
        <?php else :?>
            <span class="badge rounded-pill bg-danger">Нет</span>
            <a href="javascript:void(0);" class="small enable-site">Включить</a>
        <?php endif;?>
    </dd>
    <dt>Кеш включен</dt>
    <dd>
        <?php if (\Palto\Status::isCacheEnabled()) :?>
            <span class="badge rounded-pill bg-success">Да</span>
            <a href="javascript:void(0);" class="small disable-cache">Выключить</a>
        <?php else :?>
            <span class="badge rounded-pill bg-danger">Нет</span>
            <a href="javascript:void(0);" class="small enable-cache">Включить</a>
        <?php endif;?>
    </dd>
    <dt>Занятое место на диске</dt>
    <dd><?=\Palto\Status::getDirectoryUsePercent('/')?></dd>
</dl>

<?php $this->partial('footer.inc');