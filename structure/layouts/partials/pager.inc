<?php
/**
 * @var $this \Palto\Palto
 */
?>
<?php if ($this->previousPageUrl) : ?>
    <a href="<?=$this->previousPageUrl?>">« Previous</a>
<?php endif;?>

<?php if ($this->nextPageUrl) :?>
    <a href="<?=$this->nextPageUrl?>"> Next »</a>
<?php endif;