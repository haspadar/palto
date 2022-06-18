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
                        <?=$setting['name']?>
                        <span class="badge badge-dark"><?=$setting['group']?></span>
                        <?php if ($setting['template']) :?>
                            <span class="badge badge-secondary"><?=$setting['template']?></span>
                        <?php endif; ?>
                    </a>
                </td>
                <td>
                    <small class="text-muted">
                        <?=$setting['comment']?>
                    </small>
                </td>
                <td>
                    <a href="<?=$url?>" class="text-decoration-none">
                        <?=$setting['value']?>
                    </a>
                </td>

            </tr>

    <?php endforeach;?>
    </tbody>
</table>