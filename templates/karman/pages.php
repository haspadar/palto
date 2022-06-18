<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>


<table class="table">
    <thead>
    <tr>
        <th>Страница</th>
        <th>Шаблон</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($this->data['pages'] as $page) :?>
        <?php $url = '/karman/pages/' . $page['id'] . '?cache=0';?>
        <tr>
                <td>
                    <?php if ($page['is_enabled']) :?>
                        <span class="badge rounded-circle bg-success bulb">&nbsp;</span>
                    <?php else :?>
                        <span class="badge rounded-circle bg-danger bulb">&nbsp;</span>
                    <?php endif;?>

                    <a href="<?=$url?>" class="text-decoration-none">
                        <?=$page['comment']?>
                    </a>
                </td>
                <td>
                    <small class="text-muted">
                        <?=$page['template_name']?>
                    </small>
                </td>
            </tr>

    <?php endforeach;?>
    </tbody>
</table>