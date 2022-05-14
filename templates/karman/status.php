<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<dl class="dl-">
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
    <dd><?=\Palto\Status::getDirectoryUsePercent('/')?>%</dd>

    <dt>Запущенные процессы</dt>


    <dd>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">PID</th>
                <th scope="col">Процесс</th>
                <th scope="col">Время запуска</th>
                <th scope="col">Время работы</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach (\Palto\Status::getPhpProcesses() as $key => $phpProcess) :?>
                    <tr>
                        <th scope="row"><?=$key + 1?></th>
                        <td><?=$phpProcess['pid']?></td>
                        <td><?=$phpProcess['name']?></td>
                        <td><?=$phpProcess['run_time']->format('d.m.Y H:i:s')?></td>
                        <td><?=$phpProcess['work_time']?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">Команда для удаления: <code>kill -9 <i>PID</i></code></td>
            </tr>
            </tfoot>
        </table>

    </dd>
</dl>