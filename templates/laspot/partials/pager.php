<?php
/**
 * @var $pager \Palto\Ad
 */
?>
<?php if ($pager->getPreviousPageUrl()) : ?>
    <a href="<?=$pager->getPreviousPageUrl()?>">« <?=$this->translate('Предыдущая')?></a>
<?php endif;?>

<?php if ($pager->getNextPageUrl()) :?>
    <a href="<?=$pager->getNextPageUrl()?>"> <?=$this->translate('Следующая')?> »</a>
<?php endif;