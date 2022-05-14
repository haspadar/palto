<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<ul class="list-group list-group logs" data-directory="<?=$this->data['directory']?>" data-type="<?=$this->data['type']?>">

    <?php foreach ($this->data['logs'] as $log) :?>
        <li>
            <?=$log?>
        </li>
    <?php endforeach;?>
</ul>

