<?php /** @var $this League\Plates\Template\Template */?>

<?php $this->layout('layout');?>

<ol class="list-group list-group">

    <?php foreach ($this->data['directories'] as $directory) :?>
        <li class="list-group-item">
            <a href="/karman/log-directories/<?=$directory?>?cache=0">
                <?=$directory?>
            </a>
        </li>
    <?php endforeach;?>
</ol>
