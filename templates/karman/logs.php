<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<ul class="list-group list-group">

    <?php foreach ($this->data['logs'] as $log) :?>
        <li>
            <?=$log?>
        </li>
    <?php endforeach;?>
</ul>

