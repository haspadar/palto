<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<ol class="list-group list-group">

    <?php foreach ($this->data['types'] as $type) :?>
        <li class="list-group-item">
            <a href="/karman/log-types/<?=$this->data['directory']?>/<?=$type?>?cache=0">
                <?=$type?>
            </a>
        </li>
    <?php endforeach;?>
</ol>

