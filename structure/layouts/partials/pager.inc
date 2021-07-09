<?php
/**
 * @var $this \Palto\Palto
 */
?>
<?php if ($this->previousPageUrl) : ?>
    <a href="<?=$this->previousPageUrl?>">« предыдущая</a>
<?php endif;?>

<?php if ($this->nextPageUrl) :?>
    <a href="<?=$this->nextPageUrl?>"> следующая »</a>
<?php endif;