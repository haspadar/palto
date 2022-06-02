<?php
/**
 * @var $pager \Palto\Ad
 */
?>
<?php if ($pager->getPreviousPageUrl()) : ?>
    <a href="<?=$pager->getPreviousPageUrl()?>" class="new-obs__previous">« <?=$this->translate('Предыдущая')?></a>
<?php endif;?>

<?php if ($pager->getNextPageUrl()) :?>
    <a href="<?=$pager->getNextPageUrl()?>" class="new-obs__next"> <?=$this->translate('Следующая')?> »</a>
<?php endif;