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
        <?php if (\Palto\Config::get('AUTH') == 0) :?>
            <span class="badge rounded-pill bg-success">Да</span>
            <a href="javascript:void(0);" class="small">Выключить</a>
        <?php else :?>
            <span class="badge rounded-pill bg-danger">Нет</span>
            <a href="javascript:void(0);" class="small">Включить</a>
        <?php endif;?>
    </dd>
    <dt>Кеш включен</dt>
    <dd>
        <?php if (\Palto\Config::isCacheEnabled()) :?>
            <span class="badge rounded-pill bg-success">Да</span>
            <a href="javascript:void(0);" class="small">Выключить</a>
        <?php else :?>
            <span class="badge rounded-pill bg-danger">Нет</span>
            <a href="javascript:void(0);" class="small">Включить</a>
        <?php endif;?>
    </dd>
    <dt>Занятое место на диске</dt>
    <dd><?=\Palto\Status::getDirectoryUsePercent('/')?></dd>
</dl>

<?php $this->partial('footer.inc');