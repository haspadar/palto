<?php
/**
 * @var $this \Palto\Layout
 */
?>
<?php if ($this->getPartialVariable('previousPageUrl')) : ?>
    <a href="<?=$this->getPartialVariable('previousPageUrl')?>">« Previous</a>
<?php endif;?>

<?php if ($this->getPartialVariable('nextPageUrl')) :?>
    <a href="<?=$this->getPartialVariable('nextPageUrl')?>"> Next »</a>
<?php endif;