<?php
/**
 * @var $this \Palto\Layout\Client
 */
?>
<?php if ($this->getPartialVariable('previousPageUrl')) : ?>
    <a href="<?=$this->getPartialVariable('previousPageUrl')?>">« <?=$this->translate('Предыдущая')?></a>
<?php endif;?>

<?php if ($this->getPartialVariable('nextPageUrl')) :?>
    <a href="<?=$this->getPartialVariable('nextPageUrl')?>"> <?=$this->translate('Следующая')?> »</a>
<?php endif;