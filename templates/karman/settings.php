<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>


<table class="table">
    <thead>
    <tr>
        <th>Настройка</th>
        <th>Описание</th>
        <th>Значение</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($this->data['settings'] as $setting) :?>
        <?php $url = '/karman/settings/' . $setting['id'] . '?cache=0';?>
        <tr>
                <td>
                    <a href="<?=$url?>" class="text-decoration-none">
                        <span class="badge badge-secondary"><?=$setting['group']?></span>
                        <?=$setting['name']?>
                    </a>
                </td>
                <td>
                    <small class="text-muted">
                        <?=$setting['comment']?>
                    </small>
                </td>
                <td>
                    <?php if ($setting['type'] == 'bool' && $setting['value'] == 1) :?>
                        <span class="text-success">Да</span>
                    <?php elseif ($setting['type'] == 'bool') : ?>
                        <span class="text-danger">Нет</span>
                    <?php else : ?>
                        <?=$setting['value']?>
                    <?php endif;?>
                </td>

            </tr>

    <?php endforeach;?>
    </tbody>
</table>