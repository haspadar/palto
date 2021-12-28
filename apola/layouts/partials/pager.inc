<?php
/**
 * @var $this \Palto\Palto
 */
?>
<?php if ($this->previousPageUrl) : ?>
    <a href="<?=$this->previousPageUrl?>">« Poprzednia</a>
<?php endif;?>

<?php if ($this->nextPageUrl) :?>
    <a href="<?=$this->nextPageUrl?>"> Następna »</a>
<?php endif;