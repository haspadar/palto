<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>


<table class="table">
    <thead>
    <tr>
        <th>Шаблон</th>
        <th>Страницы</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($this->data['templates'] as $template) :?>
        <?php $url = '/karman/pages/' . $template['id'] . '?cache=0';?>
        <tr>
                <td>
                    <a href="<?=$url?>" class="text-decoration-none">
                        <?=$template['name']?>
                    </a>
                </td>
                <td>
                    <small>
                        <?php foreach ($this->data['pages'] as $page) :?>
                            <?php if ($page['template_id'] == $template['id']) :?>
                                <a href="/karman/pages/<?=$page['id']?>?cache=0">
                                    <span class="badge badge-primary">
                                        <?=$page['name']?>
                                    </span>
                                </a>
                            <?php endif;?> 
                        <?php endforeach;?>
                        
                    </small>
                </td>
            </tr>

    <?php endforeach;?>
    </tbody>
</table>