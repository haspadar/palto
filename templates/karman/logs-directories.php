<?php /** @var $this League\Plates\Template\Template */

use Palto\Logs; ?>

<?php $this->layout('layout');?>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Лог</th>
        <th>Время</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->data['directories'] as $directory) :?>
    <tr>
        <td>
            <a href="/karman/<?=$this->data['type']?>-logs/<?=$directory?>?cache=0">
                <?php if (\Palto\Status::hasPhpProcess('bin/' . $directory . '.php')) :?>
                    <span class="badge rounded-circle bg-success bulb">&nbsp;</span>
                <?php endif;?>

                <?=$directory?>
            </a>
        </td>
        <td>
            <?php if ($time = Logs::getLogLastTime($directory, $this->data['type'])) :?>
                <div class="text-muted"><?=$time->format('d.m.Y H:i:s')?></div>
            <?php endif;?>
        </td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>